<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CoursResource;
use App\Filament\Resources\EvaluationResource;
use App\Models\Classe;
use App\Models\Cours;
use App\Models\Evaluation;
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
            'dayMaxEvents' => true,
        ];
    }

    /** "Filter by class" — pick a class to see just its exams + timetable. */
    public function getHeaderActions(): array
    {
        return [
            Action::make('filterClass')
                ->label($this->classeId
                    ? (Classe::find($this->classeId)?->nom_classe ?? __('app.classe'))
                    : __('app.all_classes'))
                ->icon('heroicon-o-funnel')
                ->color($this->classeId ? 'primary' : 'gray')
                ->fillForm(fn (): array => ['classeId' => $this->classeId])
                ->schema([
                    Select::make('classeId')
                        ->label(__('app.classe'))
                        ->options(fn () => Classe::orderBy('nom_classe')->pluck('nom_classe', 'id_classe'))
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
        return collect($this->getEvaluationEvents($info))
            ->merge($this->getCoursEvents($info));
    }

    /** Exams/assessments — one all-day event on the evaluation date. */
    protected function getEvaluationEvents(FetchInfo $info): Collection
    {
        return Evaluation::query()
            ->whereNotNull('date')
            ->whereBetween('date', [$info->start, $info->end])
            ->when($this->classeId, fn ($q) => $q->where('id_classe', $this->classeId))
            ->with(['matiere'])
            ->get()
            ->map(function (Evaluation $evaluation): CalendarEvent {
                $title = $evaluation->titre ?: __('app.evaluation');
                if ($evaluation->matiere?->nom_matiere) {
                    $title .= ' — ' . $evaluation->matiere->nom_matiere;
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

    /** Weekly timetable — expand each Cours into timed occurrences in the range. */
    protected function getCoursEvents(FetchInfo $info): Collection
    {
        $cours = Cours::query()
            ->whereNotNull('jour')
            ->when($this->classeId, fn ($q) => $q->where('id_classe', $this->classeId))
            ->with(['matiere', 'classe'])
            ->get();

        $events = collect();
        $period = CarbonPeriod::create(
            Carbon::parse($info->start)->startOfDay(),
            Carbon::parse($info->end)->startOfDay(),
        );

        foreach ($period as $day) {
            foreach ($cours as $c) {
                if ((self::JOURS[strtolower((string) $c->jour)] ?? null) !== $day->dayOfWeek) {
                    continue;
                }

                $title = $c->matiere?->nom_matiere ?: __('app.cours');
                if ($c->classe?->nom_classe) {
                    $title .= ' — ' . $c->classe->nom_classe;
                }

                $events->push(
                    CalendarEvent::make($c)
                        ->title($title)
                        ->start($day->copy()->setTimeFromTimeString($c->getRawOriginal('date_debut') ?: '08:00:00'))
                        ->end($day->copy()->setTimeFromTimeString($c->getRawOriginal('date_fin') ?: '09:00:00'))
                        ->backgroundColor('#3b82f6') // blue — timetable
                );
            }
        }

        return $events;
    }
}
