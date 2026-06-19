<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CoursResource;
use App\Filament\Resources\EvaluationResource;
use App\Models\Classe;
use App\Models\Cours;
use App\Models\Evaluation;
use App\Models\Matiere;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Guava\Calendar\Filament\CalendarWidget as BaseCalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\EventClickInfo;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * School calendar (guava/calendar): evaluations (all-day, by date) + the weekly
 * timetable (recurring Cours, generated per occurrence). Click → open the record.
 */
class CalendarWidget extends BaseCalendarWidget
{
    protected bool $eventClickEnabled = true;

    /** Selected class filter (null = all classes). */
    public ?int $classeId = null;

    /** French weekday name (Cours.jour) → Carbon dayOfWeek (Sun=0 … Sat=6). */
    private const JOURS = [
        'dimanche' => 0, 'lundi' => 1, 'mardi' => 2, 'mercredi' => 3,
        'jeudi' => 4, 'vendredi' => 5, 'samedi' => 6,
    ];

    public function getOptions(): array
    {
        return [
            'headerToolbar' => [
                'start' => 'prev,next today',
                'center' => 'title',
                'end' => 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
            ],
            'height' => 800,
            'dayMaxEvents' => 4,
        ];
    }

    /** "Filter by class" — pick a class to see just its exams + timetable. */
    public function getHeaderActions(): array
    {
        return [
            Action::make('filterClass')
                ->label($this->classeId
                    ? (Classe::find($this->classeId)?->code ?? __('app.classe'))
                    : __('app.all_classes'))
                ->icon('heroicon-o-funnel')
                ->color($this->classeId ? 'primary' : 'gray')
                ->fillForm(fn (): array => ['classeId' => $this->classeId])
                ->schema([
                    Select::make('classeId')
                        ->label(__('app.classe'))
                        ->options(fn () => $this->classeOptions())
                        ->searchable()
                        ->placeholder(__('app.all_classes')),
                ])
                ->action(function (array $data): void {
                    $this->classeId = $data['classeId'] ? (int) $data['classeId'] : null;
                    $this->refreshRecords();
                }),
        ];
    }

    protected function onEventClick(EventClickInfo $info, Model $event, ?string $action = null): void
    {
        $url = $event instanceof Cours
            ? CoursResource::getUrl('edit', ['record' => $event])
            : EvaluationResource::getUrl('view', ['record' => $event]);

        $this->redirect($url);
    }

    protected function getEvents(FetchInfo $info): Collection | array | Builder
    {
        // Wrap in a base collection: an empty Eloquent\Collection (no evaluations
        // in range) would otherwise use Eloquent's merge() and call getKey() on
        // the Cours CalendarEvent value objects.
        // Exams and one-off sessions (rattrapage) are real dated events — show
        // them in every view.
        $events = collect($this->getEvaluationEvents($info))
            ->merge($this->getOneOffCoursEvents($info));

        // The weekly timetable is a *recurring* schedule, so it only reads well
        // at week/day zoom. Month and list views fetch a multi-week block — there
        // we keep an uncluttered overview instead of repeating every slot.
        if ($this->isTimetableView($info)) {
            $events = $events->merge($this->getRecurringCoursEvents($info));
        }

        return $events;
    }

    /** Week/day views fetch ≤ ~8 days; month/list fetch a multi-week block. */
    private function isTimetableView(FetchInfo $info): bool
    {
        return $info->start->diffInDays($info->end) <= 8;
    }

    /** Exams/assessments — one all-day event on the evaluation date. */
    protected function getEvaluationEvents(FetchInfo $info): Collection
    {
        return Evaluation::query()
            ->whereNotNull('date')
            ->whereBetween('date', [$info->start, $info->end])
            ->when($this->classeId, fn ($q) => $q->where('id_classe', $this->classeId))
            ->with(['matiere', 'classe'])
            ->get()
            ->map(function (Evaluation $evaluation): CalendarEvent {
                $title = $this->matiereLabel($evaluation->matiere);
                if ($evaluation->classe) {
                    $title .= ' (' . $evaluation->classe->code . ')';
                }

                $date = Carbon::parse($evaluation->date)->startOfDay();

                return CalendarEvent::make($evaluation)
                    ->title($title)
                    ->start($date)
                    ->end($date->copy()->addDay())
                    ->allDay(true)
                    ->backgroundColor('#ef4444'); // red — exams
            });
    }

    /**
     * One-off sessions (rattrapage) — Cours with a specific date. Real dated
     * events, shown in every view, in amber so they stand out from the routine.
     */
    protected function getOneOffCoursEvents(FetchInfo $info): Collection
    {
        return Cours::query()
            ->whereNotNull('date')
            ->whereBetween('date', [$info->start, $info->end])
            ->when($this->classeId, fn ($q) => $q->where('id_classe', $this->classeId))
            ->with(['matiere', 'classe'])
            ->get()
            ->map(function (Cours $c): CalendarEvent {
                $day = Carbon::parse($c->date)->startOfDay();

                return CalendarEvent::make($c)
                    ->title($this->coursTitle($c))
                    ->start($day->copy()->setTimeFromTimeString($c->getRawOriginal('date_debut') ?: '08:00:00'))
                    ->end($day->copy()->setTimeFromTimeString($c->getRawOriginal('date_fin') ?: '09:00:00'))
                    ->backgroundColor('#f59e0b'); // amber — one-off / rattrapage
            });
    }

    /**
     * Weekly timetable — expand each recurring Cours (no date) into timed
     * occurrences across the visible range. Only ever called for week/day views
     * (see getEvents), so the range is at most a few days.
     */
    protected function getRecurringCoursEvents(FetchInfo $info): Collection
    {
        $from = Carbon::parse($info->start)->startOfDay();
        $to = Carbon::parse($info->end)->startOfDay();

        // Index courses by weekday once, so the day loop is a direct lookup
        // instead of re-scanning every Cours for every day in the range.
        $coursByDay = Cours::query()
            ->whereNull('date')
            ->whereNotNull('jour')
            ->when($this->classeId, fn ($q) => $q->where('id_classe', $this->classeId))
            ->with(['matiere', 'classe'])
            ->get()
            ->groupBy(fn (Cours $c) => self::JOURS[strtolower((string) $c->jour)] ?? -1);

        $events = collect();

        foreach (CarbonPeriod::create($from, $to) as $day) {
            foreach ($coursByDay->get($day->dayOfWeek, collect()) as $c) {
                $events->push(
                    CalendarEvent::make($c)
                        ->title($this->coursTitle($c))
                        ->start($day->copy()->setTimeFromTimeString($c->getRawOriginal('date_debut') ?: '08:00:00'))
                        ->end($day->copy()->setTimeFromTimeString($c->getRawOriginal('date_fin') ?: '09:00:00'))
                        ->backgroundColor('#3b82f6') // blue — timetable
                );
            }
        }

        return $events;
    }

    /** "Subject (Code)" label for a course event — subject translated. */
    private function coursTitle(Cours $c): string
    {
        $title = $this->matiereLabel($c->matiere);

        if ($c->classe) {
            $title .= ' (' . $c->classe->code . ')';
        }

        return $title;
    }

    /** Translated subject name via its code (app.<code>), falling back to nom_matiere. */
    private function matiereLabel(?Matiere $matiere): string
    {
        if (! $matiere) {
            return __('app.cours');
        }

        $key = 'app.' . $matiere->code_matiere;
        $translated = __($key);

        return $translated === $key ? (string) $matiere->nom_matiere : $translated;
    }

    /** Class-filter options keyed by id, labelled by code, in academic order. */
    private function classeOptions(): array
    {
        return Classe::orderBy('niveau')
            ->orderBy('serie')
            ->orderBy('groupe')
            ->get()
            ->mapWithKeys(fn (Classe $c) => [$c->id_classe => $c->code])
            ->all();
    }
}
