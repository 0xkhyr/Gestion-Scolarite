# Migration Plan - Modern Stack Upgrade

## 🎯 Project Goals
Migrate from native Laravel + Bootstrap to:
- **Filament Admin Panel** (for administrators)
- **Livewire + Tailwind** (for teacher dashboard)
- **Blade + Livewire** (for public pages)
- **Laravel DomPDF** (for PDF generation)

**Strategy**: Parallel development - keep existing system running while building new interfaces

---

## 📋 Phase 0: Laravel 11 Upgrade (Prerequisites)

### 0.1 Backup Everything
```bash
# Backup database
php artisan db:backup  # or your backup method
# Commit all changes
git add .
git commit -m "Pre-Laravel 11 upgrade backup"
git checkout -b upgrade/laravel-11
```

### 0.2 Update Composer Dependencies
```bash
# Update composer.json
composer require laravel/framework:^11.0
composer require nunomaduro/collision:^8.0 --dev
composer require phpunit/phpunit:^11.0 --dev

# Update
composer update
```

### 0.3 Update Config Files
Laravel 11 streamlined configuration - many config files can be removed:

**Files to Update**:
- `bootstrap/app.php` - New structure
- `config/sanctum.php` - Update middleware
- Remove: `app/Http/Kernel.php` (moved to bootstrap/app.php)

**Run official upgrade guide commands**:
```bash
php artisan about  # Check system requirements
```

### 0.4 Test Existing Functionality
```bash
php artisan migrate:status
php artisan test  # If you have tests
# Manually test critical features
```

**Deliverables**:
- ✅ Laravel 11 installed
- ✅ All existing features working
- ✅ Database migrations successful
- ✅ No breaking changes in your code

---

## 📋 Phase 1: Foundation Setup (Week 1)

### 1.1 Install Core Packages
```bash
# Filament admin panel
composer require filament/filament:"^3.0"

# PDF generation
composer require barryvdh/laravel-dompdf

# Additional helpful packages
composer require spatie/laravel-permission
composer require spatie/laravel-activitylog
```

### 1.2 Configure Filament
```bash
php artisan filament:install --panels
php artisan make:filament-user  # Create admin user
```

### 1.3 Configure Permissions & Activity Log

#### Setup Laravel Permission
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate  # Creates roles and permissions tables
```

**Create Roles & Permissions**:
```bash
php artisan make:seeder RolesAndPermissionsSeeder
```

```php
// database/seeders/RolesAndPermissionsSeeder.php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

public function run()
{
    // Reset cached roles and permissions
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // Create permissions
    Permission::create(['name' => 'manage students']);
    Permission::create(['name' => 'manage teachers']);
    Permission::create(['name' => 'manage classes']);
    Permission::create(['name' => 'manage courses']);
    Permission::create(['name' => 'manage evaluations']);
    Permission::create(['name' => 'manage grades']);
    Permission::create(['name' => 'manage payments']);
    Permission::create(['name' => 'view reports']);
    Permission::create(['name' => 'manage settings']);

    // Create roles and assign permissions
    $admin = Role::create(['name' => 'admin'])
        ->givePermissionTo(Permission::all());

    $teacher = Role::create(['name' => 'teacher'])
        ->givePermissionTo(['manage evaluations', 'manage grades', 'view reports']);

    $student = Role::create(['name' => 'student'])
        ->givePermissionTo(['view reports']);
}
```

**Update User Model**:
```php
// app/Models/User.php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    // ...
}
```

#### Setup Activity Log
```bash
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
php artisan migrate  # Creates activity_log table
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"
```

**Configure Models to Log**:
```php
// Example: app/Models/Note.php (Grade model)
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Note extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['note', 'etudiant_id', 'evaluation_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Grade {$eventName}");
    }
}
```

**Models to Add Logging**:
- `Etudiant` (Student changes)
- `Enseignant` (Teacher changes)
- `Note` (Grade changes - critical!)
- `Evaluation` (Evaluation changes)
- `EnseignPaiement` (Payment tracking)
- `EtudePaiement` (Payment tracking)
- `Classe` (Class modifications)
- `Cours` (Course changes)

### 1.4 Setup Directory Structure
```
app/
  ├── Filament/
  │   ├── Resources/       # Admin CRUD resources
  │   ├── Pages/           # Custom admin pages
  │   └── Widgets/         # Dashboard widgets
  ├── Livewire/
  │   ├── Teacher/         # Teacher dashboard components
  │   └── Public/          # Public-facing components
  └── Services/
      └── PdfService.php   # Centralized PDF generation

resources/views/
  ├── filament/            # Filament customizations
  ├── livewire/           
  │   ├── teacher/         # Teacher views
  │   └── public/          # Public views
  ├── pdf/                 # PDF templates
  └── legacy/              # Move old views here (optional)
```

### 1.5 Configure Routes
```php
// routes/web.php - Add new route groups
Route::prefix('teacher')->middleware(['auth', 'teacher'])->group(function () {
    // Teacher Livewire routes
});

Route::prefix('student')->middleware(['auth', 'student'])->group(function () {
    // Student Livewire routes
});

// Keep existing routes for backward compatibility
```

**Deliverables**: 
- ✅ All packages installed
- ✅ Filament accessible at `/admin`
- ✅ Roles and permissions configured
- ✅ Activity logging enabled on key models
- ✅ Directory structure ready
- ✅ First admin user created with admin role

---

## 📋 Phase 2: Admin Panel Migration (Weeks 2-3)

### Priority Order (Migrate in this sequence):

#### 2.1 Core Entities (Week 2)
1. **Classes (Classes)** → `ClasseResource`
2. **Subjects (Matières)** → `MatiereResource`
3. **Teachers (Enseignants)** → `EnseignantResource`
4. **Students (Étudiants)** → `EtudiantResource`

#### 2.2 Academic Management (Week 3)
5. **Courses (Cours)** → `CoursResource`
6. **Evaluations** → `EvaluationResource`
7. **Grades (Notes)** → `NoteResource`

#### 2.3 Financial & Logs
8. **Teacher Payments** → `EnseignPaiementResource`
9. **Student Payments** → `EtudePaiementResource`
10. **Activity Logs** → `ActivityLogResource` (read-only)
11. **Administrators** → `AdministrateurResource`

#### 2.4 Access Control Resources
12. **Roles** → `RoleResource`
13. **Permissions** → `PermissionResource`

```bash
# Create permission management resources
php artisan make:filament-resource Role --generate
php artisan make:filament-resource Permission --generate
```

### For Each Resource:
```bash
php artisan make:filament-resource Etudiant --generate
```

**Customize**:
- Form fields with proper validation
- Table columns with search/filters
- Relations (e.g., Student → Class)
- Bulk actions
- Custom actions (export, import)
- **Policy-based authorization** (only admins can delete)
- **Activity log integration** (track all changes)

**Deliverables**:
- ✅ All CRUD operations in Filament
- ✅ Data relationships working
- ✅ Search and filters configured
- ✅ Role-based access control enforced
- ✅ Activity logs visible in resources
- ✅ Permission management interface ready
- ✅ Existing admin routes still functional

---

## 📋 Phase 3: Teacher Dashboard (Weeks 4-5)

### 3.1 Authentication & Authorization
```bash
php artisan make:middleware EnsureIsTeacher
```

**Implement Role-Based Middleware**:
```php
// app/Http/Middleware/EnsureIsTeacher.php
public function handle($request, Closure $next)
{
    if (!auth()->user()->hasRole('teacher')) {
        abort(403, 'Unauthorized access');
    }
    return $next($request);
}
```

**Apply to Routes**:
```php
// routes/web.php
Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/teacher/dashboard', TeacherDashboard::class);
});
```

### 3.2 Core Teacher Components

#### Week 4: Essential Features
```bash
php artisan make:livewire Teacher/Dashboard
php artisan make:livewire Teacher/MyClasses
php artisan make:livewire Teacher/MyCourses
php artisan make:livewire Teacher/StudentsList
```

**Features**:
- View assigned classes
- View assigned courses
- Student roster
- Quick stats widget

#### Week 5: Grading & Evaluation
```bash
php artisan make:livewire Teacher/EvaluationManager
php artisan make:livewire Teacher/GradeEntry
php artisan make:livewire Teacher/GradeBook
```

**Features**:
- Create evaluations
- Enter grades (bulk entry)
- View gradebook
- Grade statistics

### 3.3 Teacher Layout
Create `resources/views/layouts/teacher.blade.php` with:
- Tailwind CSS
- Alpine.js for interactions
- Teacher navigation menu
- Profile dropdown

**Deliverables**:
- ✅ Teacher login and dashboard
- ✅ All teacher features in Livewire
- ✅ Responsive Tailwind UI
- ✅ Old teacher routes still work

---

## 📋 Phase 4: PDF Generation Service (Week 6)

### 4.1 Create PDF Service
```php
// app/Services/PdfService.php
class PdfService {
    public function generateStudentTranscript($studentId) {}
    public function generateClassRoster($classeId) {}
    public function generateGradeReport($evaluationId) {}
    public function generatePaymentReceipt($paymentId) {}
}
```

### 4.2 PDF Templates
Create views in `resources/views/pdf/`:
- `student-transcript.blade.php`
- `class-roster.blade.php`
- `grade-report.blade.php`
- `payment-receipt.blade.php`

### 4.3 Integration Points
- Filament: Custom action buttons
- Teacher Dashboard: Export buttons
- Student Portal: Download transcripts

**Deliverables**:
- ✅ PDF service functional
- ✅ All templates designed
- ✅ Download buttons in UI
- ✅ Proper styling in PDFs

---

## 📋 Phase 5: Public Pages & Student Portal (Week 7)

### 5.1 Student Components
```bash
php artisan make:livewire Student/Dashboard
php artisan make:livewire Student/MyGrades
php artisan make:livewire Student/MyCourses
php artisan make:livewire Student/MyPayments
```

### 5.2 Public Pages (Light Livewire)
- Homepage (mainly Blade)
- About page
- Contact form (Livewire component)
- Course catalog (Livewire component)

**Deliverables**:
- ✅ Student can view grades
- ✅ Student can view courses
- ✅ Student can check payments
- ✅ Public pages updated

---

## 📋 Phase 6: Testing & Optimization (Week 8)

### 6.1 Testing Checklist
- [ ] All Filament resources CRUD operations
- [ ] Teacher can create evaluations
- [ ] Teacher can enter grades
- [ ] Students can view grades
- [ ] PDF generation works
- [ ] Permissions/roles enforced
- [ ] Mobile responsiveness
- [ ] Performance (N+1 queries)

### 6.2 Data Migration
- Verify all data accessible in new UI
- Test with production data dump
- Check edge cases

### 6.3 Performance
```bash
# Optimize
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check queries
npm run build  # Vite assets
```

**Deliverables**:
- ✅ All features tested
- ✅ No regressions
- ✅ Performance optimized

---

## 📋 Phase 7: Deployment & Cutover (Week 9)

### 7.1 Pre-Deployment
- [ ] Update documentation
- [ ] Train admin users on Filament
- [ ] Train teachers on new dashboard
- [ ] Prepare rollback plan

### 7.2 Deployment
```bash
# On server
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan filament:optimize
npm run build
```

### 7.3 Gradual Cutover
1. **Day 1-3**: Admin panel only (admins use Filament)
2. **Day 4-7**: Enable teacher dashboard (monitor feedback)
3. **Week 2**: Enable student portal
4. **Week 3**: Deprecate old routes (optional)

### 7.4 Remove Legacy Code (Optional - Week 10+)
Once stable, optionally remove:
- Old Bootstrap controllers
- Old views
- Unused routes

**Deliverables**:
- ✅ Production deployment successful
- ✅ Users trained
- ✅ Monitoring in place

---

## 🛡️ Risk Mitigation

### Parallel Development Benefits
- Old system continues working
- Can test new features incrementally
- Easy rollback if issues arise
- Users can transition gradually

### Rollback Strategy
- Keep all old code intact during migration
- Feature flags for new UI
- Database migrations are reversible
- Regular backups before each phase

---

## 📊 Success Metrics

- [ ] All admin operations 50% faster
- [ ] Teacher grade entry time reduced by 60%
- [ ] Mobile usage possible
- [ ] PDF generation under 2 seconds
- [ ] Zero data loss
- [ ] User satisfaction > 80%

---

## 🔧 Technical Configuration

### Required Environment Variables
```env
# Add to .env
FILAMENT_ADMIN_URL=/admin
PDF_FONT_PATH=storage/fonts
PDF_PAPER_SIZE=a4
PDF_ORIENTATION=portrait
```

### Asset Build
```json
// Update package.json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  },
  "devDependencies": {
    "@tailwindcss/forms": "^0.5.7",
    "@tailwindcss/typography": "^0.5.10",
    "tailwindcss": "^3.4.0"
  }
}
```

---

## 📝 Notes

- Keep this document updated as you progress
- Mark completed phases with dates
- Document any deviations from plan
- Add lessons learned

**Start Date**: _____________
**Target Completion**: _____________ (9-10 weeks)

---

## 🚀 Quick Start Commands

```bash
# When ready to begin:
git checkout -b feature/modern-stack-migration
composer require filament/filament:"^3.0"
composer require barryvdh/laravel-dompdf
php artisan filament:install --panels
php artisan make:filament-user

# Follow phases 1-7 sequentially
```
