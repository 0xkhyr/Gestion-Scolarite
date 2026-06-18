<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\EvaluationResource;
use App\Models\Evaluation;
use Guava\Calendar\Filament\CalendarWidget as BaseCalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * School calendar (guava/calendar) showing evaluations/exams within the
 * visible date range. Read-only; each event links to the evaluation.
 */
class CalendarWidget extends BaseCalendarWidget
{
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

                // Single all-day event on the evaluation date. NB: do NOT use
                // date_fin here — it is a time-only cast field and resolves to
                // *today*, which would put `end` before `start` and the calendar
                // hides such events. End is exclusive, so use date + 1 day.
                $date = \Illuminate\Support\Carbon::parse($evaluation->date)->startOfDay();

                return CalendarEvent::make()
                    ->title($title)
                    ->start($date)
                    ->end($date->copy()->addDay())
                    ->allDay(true)
                    ->url(
                        EvaluationResource::getUrl('view', ['record' => $evaluation->getKey()]),
                        '_self',
                    );
            });
    }
}
