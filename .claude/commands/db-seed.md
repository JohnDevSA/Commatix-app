---
description: Seed the database with sample data
argument-hint: "[seeder-class]"
---

Seed the Commatix database with sample data for development and testing.

**Options:**
- No argument: Run all seeders via DatabaseSeeder
- Specific seeder: Run a specific seeder class

**Process:**
1. Check if database has existing data
2. Warn if running in production
3. Run the appropriate seeder command
4. Show what data was created
5. Provide credentials for seeded users

**Available Seeders:**
Check `database/seeders/` for available seeders.

**Commands:**
```bash
# All seeders
php artisan db:seed

# Specific seeder
php artisan db:seed --class=UserSeeder

# Fresh migration with seed
php artisan migrate:fresh --seed
```

**Multi-Tenant Considerations:**
- Create sample tenants
- Seed data for each tenant
- Create tenant-specific users and roles
- Generate sample workflows for different industries

**After seeding:**
- Show login credentials for demo users
- List created tenants
- Show sample data statistics
