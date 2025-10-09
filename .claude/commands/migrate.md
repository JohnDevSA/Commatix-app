---
description: Run database migrations safely
argument-hint: "[fresh|refresh|rollback]"
---

Run database migrations for Commatix with proper safety checks.

**Options:**
- No argument: Run pending migrations
- `fresh`: Drop all tables and re-run migrations (⚠️ DESTRUCTIVE)
- `refresh`: Rollback and re-run all migrations (⚠️ DESTRUCTIVE)
- `rollback`: Rollback the last migration batch

**Process:**
1. Check current database connection in `.env`
2. Show pending migrations if any
3. If destructive operation, ask for confirmation
4. Run the appropriate artisan command
5. Run seeders if using fresh/refresh
6. Show migration status after completion

**Safety checks:**
- Never run destructive operations in production
- Backup data before fresh/refresh in development
- Check for tenant-specific migrations
- Verify migration status before and after
