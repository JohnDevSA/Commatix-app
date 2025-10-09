---
description: Pre-deployment checklist and validation
---

Run comprehensive pre-deployment checks for Commatix before pushing to production.

**Deployment Checklist:**

1. **Code Quality**
   ```bash
   composer lint
   composer test
   composer grumphp
   ```

2. **Security Checks**
   - Verify `.env.example` is up to date
   - Check no sensitive data in version control
   - Verify API keys are in environment variables
   - Check `composer audit` for vulnerabilities

3. **Database**
   - Verify all migrations are tracked
   - Check for pending migrations
   - Verify seeders are production-safe
   - Test migration rollback capability

4. **Dependencies**
   - Run `composer outdated` to check for updates
   - Run `npm outdated` for frontend dependencies
   - Review breaking changes in updates

5. **Configuration**
   - Verify production environment variables
   - Check queue configuration
   - Verify cache drivers (Redis recommended for production)
   - Check session driver configuration
   - Verify file storage configuration

6. **Multi-Tenancy**
   - Test tenant isolation
   - Verify tenant migrations work
   - Check tenant-specific configurations

7. **Performance**
   - Run `php artisan config:cache`
   - Run `php artisan route:cache`
   - Run `php artisan view:cache`
   - Verify database indexes exist
   - Check N+1 query issues with Telescope

**Generate deployment report** showing status of all checks.
