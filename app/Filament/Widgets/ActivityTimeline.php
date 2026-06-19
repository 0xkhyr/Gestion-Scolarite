<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ActivityLogResource;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use LaraZeus\ActivityTimeline\Components\ActivityDate;
use LaraZeus\ActivityTimeline\Components\ActivityDescription;
use LaraZeus\ActivityTimeline\Components\ActivityIcon;
use LaraZeus\ActivityTimeline\Components\ActivitySection;
use LaraZeus\ActivityTimeline\Components\ActivityTitle;
use Spatie\Activitylog\Models\Activity as ActivityModel;

class ActivityTimeline extends Widget implements HasSchemas
{
    use InteractsWithSchemas;

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.activity-timeline';

    public static function canView(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin', 'director']);
    }

    /** Latest activity rendered with the lara-zeus activity-timeline components. */
    public function activitiesInfolist(Schema $schema): Schema
    {
        return $schema
            ->state(['activities' => $this->getActivities()])
            ->components([
                ActivitySection::make('activities')
                    ->label(__('app.fil_activite'))
                    ->schema([
                        ActivityTitle::make('title')->allowHtml(),
                        ActivityDescription::make('description')->allowHtml(),
                        ActivityDate::make('created_at')->date('d M Y H:i'),
                        ActivityIcon::make('event')
                            ->icon(fn (?string $state): string => match ($state) {
                                'created' => 'heroicon-m-plus-circle',
                                'updated' => 'heroicon-m-pencil-square',
                                'deleted' => 'heroicon-m-trash',
                                default => 'heroicon-m-bolt',
                            })
                            ->color(fn (?string $state): string => match ($state) {
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                default => 'gray',
                            }),
                    ]),
            ]);
    }

    /** Map Spatie activities into the timeline's expected state shape. */
    protected function getActivities(): array
    {
        return ActivityModel::latest()->limit(10)->get()->map(function (ActivityModel $a) {
            $resource = $a->subject
                ? class_basename($a->subject_type) . " #{$a->subject_id}"
                : ($a->properties['resource'] ?? null);

            $causer = $a->causer?->name
                ?? ($a->causer_type ? class_basename($a->causer_type) . " #{$a->causer_id}" : '—');

            $label = e($a->description ?: __('app.activity'));
            if ($resource) {
                $label .= " — <span class='text-gray-500 dark:text-gray-400'>" . e($resource) . '</span>';
            }

            // Each entry links to its full activity-log detail page.
            $url = ActivityLogResource::getUrl('view', ['record' => $a->getKey()]);
            $title = "<a href='" . e($url) . "' class='hover:underline'>{$label}</a>";

            return [
                'title' => $title,
                'description' => e($causer),
                'event' => $a->event,
                'created_at' => $a->created_at,
            ];
        })->all();
    }
}
