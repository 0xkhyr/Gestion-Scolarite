# Architecture Refactoring: Profile Identity Migration

## Overview
This migration moves identity fields (nom, prenom, telephone, adresse) from the `users` table to their respective profile tables (etudiants, enseignants, administratifs).

## Architecture Changes

### Before
- `users` table contained both authentication AND identity data
- Profile tables only had specific data (matricule, date_naissance, etc.)
- Students/teachers couldn't exist without a user account

### After
- `users` table contains ONLY authentication data (email, password, is_active)
- Profile tables contain identity data (nom, prenom, telephone, adresse)
- Profiles can exist independently of user accounts
- User accounts are optional and linked via polymorphic relationship

## Migration Steps

### 1. Run Migrations in Order

```bash
php artisan migrate
```

This will execute 4 migrations in sequence:
1. `2026_01_22_190000` - Add identity columns to profile tables
2. `2026_01_22_190100` - Migrate data from users to profiles
3. `2026_01_22_190200` - Make profile identity fields NOT NULL
4. `2026_01_22_190300` - Add unique index on (profile_type, profile_id)

### 2. Verify Data Migration

Check that data was copied correctly:

```sql
-- Check students
SELECT e.matricule, e.nom, e.prenom, u.email 
FROM etudiants e 
LEFT JOIN users u ON u.profile_type = 'App\\Models\\Etudiant' AND u.profile_id = e.id_etudiant
LIMIT 10;

-- Check teachers
SELECT ens.nom, ens.prenom, u.email 
FROM enseignants ens 
LEFT JOIN users u ON u.profile_type = 'App\\Models\\Enseignant' AND u.profile_id = ens.id_enseignant
LIMIT 10;
```

### 3. Clear Caches

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 4. Test the Application

1. **Create a new student WITHOUT account**:
   - Go to /admin/etudiants/create
   - Fill in nom, prenom, classe, etc.
   - Leave the "User Account" section collapsed/empty
   - Save → Student should be created without a user account

2. **Create a student WITH account**:
   - Go to /admin/etudiants/create
   - Fill in identity fields
   - Expand "User Account" section
   - Add email and password
   - Save → Student created with linked user account

3. **Edit existing student**:
   - Edit any existing student
   - Verify nom/prenom are editable
   - Verify matricule is displayed but disabled
   - If student has account, email/password section should be visible

## Model Changes

### Etudiant Model
- Added: `nom`, `prenom`, `telephone`, `adresse` to `$fillable`
- Removed: Old accessors that fetched from user relationship
- Added: `hasAccount()` method to check if user exists
- Added: `getFullNameAttribute()` helper

### User Model
- Identity fields (nom, prenom, telephone, adresse) are now DEPRECATED
- These fields are kept temporarily for backward compatibility
- Will be removed in future version after full migration

## Form Changes

### EtudiantResource Form
- Identity fields (nom, prenom, telephone, adresse) are now direct fields
- User account section is optional and collapsible
- Password field only required on creation (if providing account)
- Email/password can be left empty to create student without account

### Table Columns
- Changed `user.nom` → `nom` (direct column)
- Changed `user.prenom` → `prenom` (direct column)
- Changed `user.telephone` → `telephone` (direct column)
- `user.email` shows "No account" if user doesn't exist
- `user.is_active` shows checkmark only if account exists

## Database Constraints

- **Unique Index**: A profile can have maximum ONE user account
  - Enforced by unique index on `(profile_type, profile_id)`
- **Required Fields in Profiles**: nom, prenom are NOT NULL
- **Optional Fields**: telephone, adresse, email, password are nullable

## Backward Compatibility

- Existing authentication still works
- Old data is preserved in both locations temporarily
- Queries using `etudiant.user.nom` will still work (via relationship)
- Queries can now use `etudiant.nom` directly (preferred)

## Future Steps (Optional)

After verifying everything works for 1-2 weeks:

1. Create migration to drop deprecated columns from `users`:
   ```php
   Schema::table('users', function (Blueprint $table) {
       $table->dropColumn(['nom', 'prenom', 'telephone', 'adresse']);
   });
   ```

2. Remove deprecated accessors from User model

## Rollback Plan

If issues occur:

```bash
php artisan migrate:rollback --step=4
```

This will:
1. Remove unique index
2. Make profile fields nullable again
3. Keep data in profile tables (safe)
4. Remove columns from profile tables

**NOTE**: Data is duplicated during migration for safety. The rollback won't restore users table data, but original data is preserved.

## FAQ

**Q: Can I still create users without profiles?**
A: Yes, super_admin users can exist without profiles.

**Q: What happens to existing students?**
A: Their data is copied to profile tables. Both locations have the data temporarily.

**Q: Do I need to update my queries?**
A: Eventually yes. Start using `$etudiant->nom` instead of `$etudiant->user->nom`.

**Q: Will authentication break?**
A: No, authentication uses email/password which remain in users table.

**Q: Can a student have multiple accounts?**
A: No, the unique index prevents this.

## Support

If you encounter issues:
1. Check migration status: `php artisan migrate:status`
2. Check logs: `storage/logs/laravel.log`
3. Verify database schema matches expectations
4. Test with a fresh student creation
