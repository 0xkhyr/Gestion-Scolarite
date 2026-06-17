<?php

namespace App\Listeners;

use App\Services\ActivityLogger;
use Spatie\Permission\Events\PermissionAttached;
use Spatie\Permission\Events\PermissionDetached;
use Spatie\Permission\Events\RoleAttached;
use Spatie\Permission\Events\RoleDetached;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Audit every role/permission assignment change. These are the most
 * security-sensitive events in the app (who granted whom admin access).
 */
class LogPermissionChange
{
    public function handleRoleAttached(RoleAttached $event): void
    {
        $names = $this->resolveNames($event->rolesOrIds, Role::class);

        ActivityLogger::record(
            'security',
            'Roles granted: ' . implode(', ', $names),
            $event->model,
            ['type' => 'role_attached', 'roles' => $names],
        );
    }

    public function handleRoleDetached(RoleDetached $event): void
    {
        $names = $this->resolveNames($event->rolesOrIds, Role::class);

        ActivityLogger::record(
            'security',
            'Roles revoked: ' . implode(', ', $names),
            $event->model,
            ['type' => 'role_detached', 'roles' => $names],
        );
    }

    public function handlePermissionAttached(PermissionAttached $event): void
    {
        $names = $this->resolveNames($event->permissionsOrIds, Permission::class);

        ActivityLogger::record(
            'security',
            'Permissions granted: ' . implode(', ', $names),
            $event->model,
            ['type' => 'permission_attached', 'permissions' => $names],
        );
    }

    public function handlePermissionDetached(PermissionDetached $event): void
    {
        $names = $this->resolveNames($event->permissionsOrIds, Permission::class);

        ActivityLogger::record(
            'security',
            'Permissions revoked: ' . implode(', ', $names),
            $event->model,
            ['type' => 'permission_detached', 'permissions' => $names],
        );
    }

    /**
     * The events may carry names, ids, models or a collection. Normalise to readable names.
     */
    protected function resolveNames(mixed $rolesOrIds, string $modelClass): array
    {
        $items = $rolesOrIds instanceof \Illuminate\Support\Collection
            ? $rolesOrIds->all()
            : (is_array($rolesOrIds) ? $rolesOrIds : [$rolesOrIds]);

        return collect($items)
            ->map(function ($item) use ($modelClass) {
                if (is_object($item)) {
                    return $item->name ?? (string) ($item->id ?? '?');
                }

                if (is_int($item) || ctype_digit((string) $item)) {
                    return optional($modelClass::find($item))->name ?? "#{$item}";
                }

                return (string) $item;
            })
            ->filter()
            ->values()
            ->all();
    }
}
