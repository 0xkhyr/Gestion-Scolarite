<?php

namespace App\Filament\Widgets;

use Spatie\Activitylog\Models\Activity as ActivityModel;
use Filament\Widgets\Widget;

class ActivityTimeline extends Widget
{
    protected static ?int $sort = 4;

    public static function canView(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin', 'director']);
    }

    protected int | string | array $columnSpan = 'half';

    protected string $view = 'filament.widgets.activity-timeline';

    public function getActivities()
    {
        return ActivityModel::latest()->limit(10)->get()->map(function (ActivityModel $a) {
            // Normalize action (map common events to simplified actions used by UI)
            $event = $a->event ?? null;
            $action = match($event) {
                'created' => 'create',
                'deleted' => 'delete',
                'updated' => 'update',
                default => $event ?? 'event',
            };

            // Resolve resource (subject) or fallback to properties.resource
            $resource = $a->subject ? class_basename($a->subject_type) . " #{$a->subject_id}" : ($a->properties['resource'] ?? null);

            // Resolve user display (causer name or type)
            $userDisplay = $a->causer?->name ?? ($a->causer_type ? class_basename($a->causer_type) . " #{$a->causer_id}" : null);

            // Attach computed fields on the model instance for the view to use
            $a->action = $action;
            $a->resource = $resource;
            $a->user_type = $userDisplay;

            return $a;
        });
    }
}
