<?php

namespace App\Services;

use Throwable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogger
{
    /**
     * Record an audit event caused by the currently authenticated user.
     * Standardises the log name + ip/user-agent properties read by the
     * ActivityLogResource view page.
     *
     * @param Model|null $subject
     */
    public static function record(string $logName, string $description, $subject = null, array $properties = []): void
    {
        $chain = activity($logName)->causedBy(auth()->user());

        if ($subject) {
            $chain->performedOn($subject);
        }

        $chain
            ->withProperties(array_merge([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ], $properties))
            ->log($description);
    }

    /**
     * Log activity using Spatie activitylog. Keeps compatibility with old signature.
     */
    public static function log(string $userType, ?int $userId, string $action, ?string $resource = null, $resourceId = null, ?string $description = null, ?array $changes = null, ?Request $request = null)
    {
        $ip = $request?->ip() ?? request()->ip() ?? null;
        $ua = $request?->userAgent() ?? request()->userAgent() ?? null;

        // Try to resolve causer (user model) if possible
        $causer = null;
        if ($userType && $userId && class_exists($userType)) {
            try {
                $causer = $userType::find($userId);
            } catch (Throwable $e) {
                $causer = null;
            }
        }

        // Try to resolve performedOn subject if resource is a model class or model instance
        $performedOn = null;
        if ($resource instanceof Model) {
            $performedOn = $resource;
        } elseif (is_string($resource) && $resource && $resourceId && class_exists($resource)) {
            try {
                $performedOn = $resource::find($resourceId);
            } catch (Throwable $e) {
                $performedOn = null;
            }
        }

        $properties = array_filter([
            'resource' => is_string($resource) && ! $performedOn ? $resource : null,
            'resource_id' => $resourceId,
            'changes' => $changes,
            'ip' => $ip,
            'user_agent' => $ua,
        ], fn($v) => !is_null($v));

        $activityChain = activity();

        if ($causer) {
            $activityChain = $activityChain->causedBy($causer);
        }

        $activity = $activityChain
            ->performedOn($performedOn ?? null)
            ->withProperties($properties)
            ->log($description ?? $action);

        return $activity;
    }
}
