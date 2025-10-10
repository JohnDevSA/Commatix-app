---
description: Start the development environment
---

Start all development servers for Commatix (Laravel, Queue, Vite).

**Execute:**
```bash
composer dev
```

This runs the concurrently command that starts:
1. **Laravel Server** (php artisan serve) - Port 8000
2. **Queue Worker** (php artisan queue:work --tries=1) - Processes jobs
3. **Vite Dev Server** (pnpm run dev) - Hot module replacement for frontend

**After starting:**
- Show the URLs where services are running
- Mention that logs will appear in the terminal
- Remind about stopping with Ctrl+C
- Verify all 3 services started successfully

**Common issues:**
- Port 8000 already in use: Try `php artisan serve --port=8001`
- Queue worker errors: Check database connection
- Vite errors: Run `pnpm install` first
