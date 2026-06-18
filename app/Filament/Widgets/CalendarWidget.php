<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\EvaluationResource;
use App\Models\Evaluation;
use Guava\Calendar\Filament\CalendarWidget as BaseCalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\EventClickInfo;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * School calendar (guava/calendar) showing evaluations/exams. Read-only;
 * clicking an event opens the evaluation. Month/week/day/list views via toolbar.
 */
class CalendarWidget extends BaseCalendarWidget
{
    protected bool $eventClickEnabled = true;

    /** Toolbar with prev/next/today + month/week/day/list view switcher. */
    public function getOptions(): array
    {
        return [
            'headerToolbar' => [
                'start' => 'prev,next today',
                'center' => 'title',
                'end' => 'dayGridMonth,timeGridWeek,timeGridDay,listMonth',
            ],
        ];
    }

    /** Click an evaluation event → open its page. */
    protected function onEventClick(EventClickInfo $info, Model $event, ?string $action = null): void
    {
        $this->redirect(EvaluationResource::getUrl('view', ['record' => $event]));
    }

    protected function getEvents(FetchInfo $info): Collection | array | Builder
    {
        return Evaluation::query()
            ->whereNotNull('date')
            ->whereBetween('date', [$info->start, $info->end])
            ->with(['matiere', 'classe'])
            ->get()
            ->map(function (Evaluation $evaluation): CalendarEvent {
                $title = $evaluation->titre ?: __('app.evaluation');

                if ($evaluation->matiere?->nom_matiere) {
                    $title .= ' — ' . $evaluation->matiere->nom_matiere;
                }

                // All-day event on the evaluation date (end is exclusive → +1 day).
                $date = Carbon::parse($evaluation->date)->startOfDay();

                // Model-backed so a click can resolve the record (Guava event-click).
                return CalendarEvent::make($evaluation)
                    ->title($title)
                    ->start($date)
                    ->end($date->copy()->addDay())
                    ->allDay(true);
            });
    }
}
