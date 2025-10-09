---
description: Clear all application caches
---

Clear all caches in Commatix to resolve caching issues.

**Execute these commands in sequence:**

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

**Optional Redis cache clear:**
If using Redis (check .env for CACHE_DRIVER=redis):
```bash
php artisan redis:clear
```

**When to use this:**
- After changing configuration files
- After updating .env variables
- When seeing stale data
- After pulling new code
- Before deployment

**Follow-up optimization:**
After clearing, you may want to rebuild caches:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

⚠️ **Note**: Only cache config/routes in production, not in development.
