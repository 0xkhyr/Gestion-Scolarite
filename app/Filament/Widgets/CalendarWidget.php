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

                return CalendarEvent::make()
                    ->title($title)
                    ->start($evaluation->date)
                    ->end($evaluation->date_fin ?: $evaluation->date)
                    ->allDay(true)
                    ->url(
                        EvaluationResource::getUrl('view', ['record' => $evaluation->getKey()]),
                        '_self',
                    );
            });
    }
}
