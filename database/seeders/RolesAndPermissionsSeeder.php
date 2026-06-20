<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create comprehensive permissions organized by resource with dot notation
        $permissions = [
            // Student Management
            'student.view',
            'student.create', 
            'student.edit',
            'student.delete',
            'student.manage_accounts',
            'student.view_grades',
            'student.export',
            
            // Teacher Management
            'teacher.view',
            'teacher.create',
            'teacher.edit', 
            'teacher.delete',
            'teacher.manage_accounts',
            'teacher.assign_subjects',
            
            // Class Management
            'class.view',
            'class.create',
            'class.edit',
            'class.delete',
            'class.manage',
            
            // Subject Management
            'subject.view',
            'subject.create',
            'subject.edit',
            'subject.delete',
            
            // Course & Schedule Management
            'course.view',
            'course.create',
            'course.edit',
            'course.delete',
            'timetable.view',
            'timetable.manage',
            
            // Evaluation Management
            'evaluation.view',
            'evaluation.create',
            'evaluation.edit',
            'evaluation.delete',
            'evaluation.view_all',
            
            // Grade Management
            'grade.view',
            'grade.create',
            'grade.edit',
            'grade.delete',
            'grade.manage', // Bulk grade entry permission
            'grade.view_all',
            'grade.edit_comments',
            
            // Payment & Financial Management
            'payment.view',
            'payment.create',
            'payment.edit',
            'payment.delete',
            'payment.manage_reports',
            
            // Transcripts (relevé de notes)
            'transcript.view',
            'transcript.export',

            // System Administration
            'user.manage',
            'role.manage',
            'permission.manage',
            'activity_log.view',
            'system.manage_settings',
            'setting.view',
            'setting.manage',
            'backup.view',
            'backup.create',
            'backup.download',
            'backup.delete',
            'page.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles with specific purposes and assign permissions
        
        // 1. SUPER ADMIN - Full system access
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. ADMIN - Administrative management with system settings
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            // Student management
            'student.view', 'student.create', 'student.edit', 'student.delete',
            'student.manage_accounts', 'student.export',
            // Teacher management
            'teacher.view', 'teacher.create', 'teacher.edit', 'teacher.delete',
            'teacher.manage_accounts',
            // Academic management
            'class.view', 'class.create', 'class.edit', 'class.delete', 'class.manage',
            'subject.view', 'subject.create', 'subject.edit', 'subject.delete',
            'course.view', 'course.create', 'course.edit', 'course.delete', 'timetable.view', 'timetable.manage',
            // System administration
            'user.manage', 'role.manage', 'permission.manage', 'activity_log.view', 'system.manage_settings',
            'setting.view', 'setting.manage',
            'page.manage',
            // Reports
            'transcript.view', 'transcript.export',
        ]);

        // 3. DIRECTOR - Full academic management, no system administration  
        $director = Role::firstOrCreate(['name' => 'director']);
        $director->givePermissionTo([
            // Student management
            'student.view', 'student.create', 'student.edit', 'student.delete',
            'student.manage_accounts', 'student.view_grades', 'student.export',
            // Teacher management
            'teacher.view', 'teacher.create', 'teacher.edit', 'teacher.delete',
            'teacher.manage_accounts', 'teacher.assign_subjects',
            // Academic management
            'class.view', 'class.create', 'class.edit', 'class.delete', 'class.manage',
            'subject.view', 'subject.create', 'subject.edit', 'subject.delete',
            'course.view', 'course.create', 'course.edit', 'course.delete', 'timetable.view', 'timetable.manage',
            // Evaluation & grades
            'evaluation.view', 'evaluation.create', 'evaluation.edit', 'evaluation.delete', 'evaluation.view_all',
            'grade.view', 'grade.create', 'grade.edit', 'grade.delete', 'grade.manage', 'grade.view_all', 'grade.edit_comments',
            // Reports
            'transcript.view', 'transcript.export',
            // Payments
            'payment.view', 'payment.create', 'payment.edit', 'payment.delete', 'payment.manage_reports',
            // Activity monitoring
            'activity_log.view',
        ]);

        // 4. ACADEMIC COORDINATOR - Academic content management
        $coordinator = Role::firstOrCreate(['name' => 'academic_coordinator']);
        $coordinator->givePermissionTo([
            // Academic structure
            'class.view', 'class.create', 'class.edit', 'class.manage',
            'subject.view', 'subject.create', 'subject.edit', 
            'course.view', 'course.create', 'course.edit', 'timetable.view', 'timetable.manage',
            // Teachers (limited)
            'teacher.view', 'teacher.assign_subjects',
            // Students (view and basic edit)
            'student.view', 'student.edit', 'student.view_grades',
            // Evaluations
            'evaluation.view', 'evaluation.create', 'evaluation.edit', 'evaluation.view_all',
            'grade.view', 'grade.view_all',
            // Reports
            'transcript.view',
        ]);

        // 5. TEACHER - Classroom management
        $teacher = Role::firstOrCreate(['name' => 'teacher']); 
        $teacher->givePermissionTo([
            // Students (their classes only)
            'student.view', 'student.view_grades',
            // Classes (their classes only)
            'class.view', 'course.view', 'timetable.view',
            // Subjects
            'subject.view',
            // Evaluations (their subjects only)
            'evaluation.view', 'evaluation.create', 'evaluation.edit',
            'grade.view', 'grade.create', 'grade.edit', 'grade.manage', 'grade.edit_comments',
            // Basic reports
            'transcript.view',
        ]);

        // 6. SECRETARY - Administrative support
        $secretary = Role::firstOrCreate(['name' => 'secretary']);
        $secretary->givePermissionTo([
            // Students (basic management)
            'student.view', 'student.create', 'student.edit',
            // Teachers (basic info)
            'teacher.view', 'teacher.create', 'teacher.edit',
            // Classes
            'class.view', 'course.view', 'timetable.view', 'timetable.manage',
            // Basic reports
            'transcript.view', 'transcript.export',
            // Payments
            'payment.view', 'payment.create', 'payment.edit',
        ]);

        // 7. ACCOUNTANT - Financial management
        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountant->givePermissionTo([
            // Students (for payment purposes)
            'student.view',
            // Teachers (for salary purposes) 
            'teacher.view',
            // Full payment management
            'payment.view', 'payment.create', 'payment.edit', 'payment.delete',
            'payment.manage_reports',
            // Financial reports
            'transcript.view', 'transcript.export',
        ]);

        // 8. STUDENT - Self-service access
        $student = Role::firstOrCreate(['name' => 'student']);
        $student->givePermissionTo([
            'student.view_grades', // Their own grades only
            'course.view', // Their class courses
        ]);

        $this->command->info(Role::count() . ' roles, ' . Permission::count() . ' permissions seeded.');
    }
}

