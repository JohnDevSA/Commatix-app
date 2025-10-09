---
description: Monitor and manage queue workers
argument-hint: "[start|status|failed|retry]"
---

Manage queue workers for Commatix background job processing.

**Options:**
- `start`: Start a queue worker
- `status`: Show queue status and statistics
- `failed`: List failed jobs
- `retry`: Retry failed jobs

**Commands:**

**Start worker:**
```bash
php artisan queue:work --tries=3 --timeout=90
```

**Check queue status:**
```bash
php artisan queue:work --once  # Process one job
php artisan queue:listen        # Auto-reload on code changes (dev only)
```

**Failed jobs:**
```bash
php artisan queue:failed        # List all failed jobs
php artisan queue:retry {id}    # Retry specific job
php artisan queue:retry all     # Retry all failed jobs
php artisan queue:flush         # Delete all failed jobs
```

**Queue tables:**
```bash
php artisan queue:table         # Create queue table migration
php artisan queue:failed-table  # Create failed jobs table
```

**Monitoring:**
- Show number of pending jobs
- Show recently failed jobs
- Show job processing statistics
- Suggest Laravel Horizon for advanced monitoring (already in composer.json)

**Common queue jobs in Commatix:**
- Email notifications
- SMS sending
- WhatsApp messages
- Workflow task scheduling
- Report generation
- Data exports
