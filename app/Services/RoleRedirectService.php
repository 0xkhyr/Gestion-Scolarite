<?php

namespace App\Services;

class RoleRedirectService
{
    public static function getRedirectPath($user): string
    {
        if (!$user) {
            return '/';
        }

        // Admin Panel - Administrative Management + Operational Staff (merged)
        if ($user->hasAnyRole(['super_admin', 'admin', 'director', 'academic_coordinator', 'secretary', 'accountant'])) {
            return '/admin';
        }

        // Teacher Panel - Teaching Staff
        if ($user->hasAnyRole(['teacher', 'enseignant'])) {
            return '/teacher';
        }

        // Student or other roles - Homepage for now
        return '/';
    }

    public static function getLoginUrl($request): string
    {
        // Check the current path to determine which login form to show
        if ($request->is('admin') || $request->is('admin/*')) {
            return route('filament.admin.auth.login');
        }

        if ($request->is('teacher') || $request->is('teacher/*')) {
            return route('filament.teacher.auth.login');
        }

        // Default to general login
        return route('login');
    }
}