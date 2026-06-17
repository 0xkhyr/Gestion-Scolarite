<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * The only "report" feature is the student transcript (relevé de notes).
     * Rename the two real permissions to transcript.* (renaming the row keeps
     * existing role assignments, which reference permission_id), and drop the
     * two phantom permissions that gated nothing.
     */
    public function up(): void
    {
        // Rename real permissions (preserves role/user assignments).
        DB::table('permissions')->where('name', 'report.view')->update(['name' => 'transcript.view']);
        DB::table('permissions')->where('name', 'report.export')->update(['name' => 'transcript.export']);

        // Remove phantom permissions + any assignments to them.
        $phantomIds = DB::table('permissions')
            ->whereIn('name', ['report.generate', 'report.view_statistics'])
            ->pluck('id');

        if ($phantomIds->isNotEmpty()) {
            DB::table('role_has_permissions')->whereIn('permission_id', $phantomIds)->delete();
            DB::table('model_has_permissions')->whereIn('permission_id', $phantomIds)->delete();
            DB::table('permissions')->whereIn('id', $phantomIds)->delete();
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        DB::table('permissions')->where('name', 'transcript.view')->update(['name' => 'report.view']);
        DB::table('permissions')->where('name', 'transcript.export')->update(['name' => 'report.export']);

        foreach (['report.generate', 'report.view_statistics'] as $name) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                ['created_at' => now(), 'updated_at' => now()],
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
