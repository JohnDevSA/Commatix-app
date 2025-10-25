# Kubernetes Troubleshooting Guide

Common issues and solutions when running Commatix queue workers in Kubernetes.

---

## 🔴 Jobs Stuck in "Pending" Status

**Symptom:** Jobs dispatched from Laravel appear in Telescope but show status "pending" forever. Queue workers are running but not processing jobs.

**Root Cause:** Laravel app and Kubernetes workers are using **different Redis instances**.

### How to Diagnose

1. Check what Redis the Laravel app is using:
```bash
docker exec commatix-laravel.test-1 php artisan tinker --execute="echo config('database.redis.default.host');"
```

2. Check what Redis the K8s workers are using:
```bash
kubectl exec deployment/commatix-queue-default -n commatix -- php artisan tinker --execute="echo config('database.redis.default.host');"
```

3. If they're different (e.g., `redis` vs `commatix-redis`), that's your problem!

### Solution

Both Laravel and workers must use the **same Redis instance**. The recommended setup is to use Sail's Redis for both:

**Update `k8s/queue-workers-deployment.yaml`:**
```yaml
env:
  - name: REDIS_HOST
    value: "host.docker.internal"  # Use Sail Redis
  - name: REDIS_PORT
    value: "6379"
```

**Apply changes:**
```bash
kubectl apply -f k8s/queue-workers-deployment.yaml
kubectl rollout restart deployment/commatix-queue-default -n commatix
kubectl rollout restart deployment/commatix-queue-campaigns -n commatix
```

**Verify it's working:**
```bash
# Dispatch a test job
docker exec commatix-laravel.test-1 php artisan tinker --execute="App\Jobs\TestKubernetesJob::dispatch();"

# Watch it get processed (should see output within seconds)
kubectl logs -f deployment/commatix-queue-default -n commatix --tail=20
```

---

## 🔴 Workers Can't Connect to MySQL

**Symptom:** Worker pods crash with error:
```
SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo for commatix-mysql-1 failed: Name or service not known
```

**Root Cause:** Kubernetes pods can't resolve Docker Compose service names (`commatix-mysql-1`) because they're in different networks.

### Solution

Use `host.docker.internal` to access MySQL running in Sail from Kubernetes pods.

**Update `k8s/queue-workers-deployment.yaml`:**
```yaml
env:
  - name: DB_HOST
    value: "host.docker.internal"  # Point to Sail MySQL
  - name: DB_PORT
    value: "3306"
```

**Apply and restart:**
```bash
kubectl apply -f k8s/queue-workers-deployment.yaml
kubectl rollout restart deployment/commatix-queue-default -n commatix
```

**Test connection:**
```bash
kubectl exec deployment/commatix-queue-default -n commatix -- php artisan tinker --execute="echo 'Connected: ' . DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_INFO);"
```

---

## 🔴 Kubernetes Not Accessible from WSL

**Symptom:**
```bash
kubectl get pods
# Error: dial tcp: lookup kubernetes.docker.internal: no such host
```

**Root Cause:** WSL can't resolve `kubernetes.docker.internal` hostname.

### Solution

Update kubectl config to use `127.0.0.1` instead:

```bash
kubectl config set-cluster docker-desktop \
  --server=https://127.0.0.1:6443 \
  --insecure-skip-tls-verify=true

# Test connection
kubectl cluster-info
kubectl get nodes
```

---

## 🔴 Worker Pods CrashLoopBackOff

**Symptom:**
```bash
kubectl get pods -n commatix
# NAME                                    READY   STATUS             RESTARTS
# commatix-queue-default-xxx-yyy          0/1     CrashLoopBackOff   5
```

### Possible Causes & Solutions

**1. Missing `artisan` file**

Check logs:
```bash
kubectl logs <pod-name> -n commatix
# Could not open input file: artisan
```

**Solution:** Ensure Dockerfile copies code to `/var/www/html`:
```dockerfile
FROM sail-8.4/app:latest
COPY --chown=sail:sail . /var/www/html
WORKDIR /var/www/html
```

**2. Database connection failure**

Check logs for MySQL errors. Ensure:
- Sail MySQL is running (`docker ps | grep mysql`)
- `DB_HOST=host.docker.internal` in deployment YAML
- MySQL port 3306 is exposed (`docker port commatix-mysql-1`)

**3. Redis connection failure**

Check logs for Redis errors. Ensure:
- Sail Redis is running (`docker ps | grep redis`)
- `REDIS_HOST=host.docker.internal` in deployment YAML
- Redis port 6379 is exposed

### Debugging CrashLoopBackOff

```bash
# Get detailed pod info
kubectl describe pod <pod-name> -n commatix

# Check container logs (including previous crashes)
kubectl logs <pod-name> -n commatix --previous

# Exec into a running pod to debug
kubectl exec -it <pod-name> -n commatix -- /bin/bash
php artisan tinker
```

---

## 🔴 No Worker Logs Visible

**Symptom:**
```bash
kubectl logs deployment/commatix-queue-default -n commatix
# (no output)
```

**Root Causes:**

### 1. Workers Running but No Jobs

If workers are healthy but just waiting for jobs, you won't see output. Verify:

```bash
# Check if workers are running
kubectl exec deployment/commatix-queue-default -n commatix -- ps aux | grep queue

# Should show:
# php artisan queue:work redis --queue=default --tries=3 --timeout=90 --verbose
```

### 2. Different Redis Instances

Workers are listening to wrong Redis. See "Jobs Stuck in Pending" section above.

### 3. Logs Going to stderr

Try checking stderr:
```bash
kubectl logs deployment/commatix-queue-default -n commatix --all-containers=true
```

---

## 🔴 "Image Not Found" or "ImagePullBackOff"

**Symptom:**
```bash
kubectl get pods -n commatix
# NAME                                    READY   STATUS             RESTARTS
# commatix-queue-default-xxx-yyy          0/1     ImagePullBackOff   0
```

**Root Cause:** Image doesn't exist locally or `imagePullPolicy` is incorrect.

### Solution

1. **Build the image:**
```bash
docker build -f Dockerfile.k8s-sail -t commatix-queue:latest .
```

2. **Verify image exists:**
```bash
docker images | grep commatix-queue
# Should show: commatix-queue  latest  ...
```

3. **Ensure `imagePullPolicy: Never` in deployment:**
```yaml
spec:
  containers:
    - name: queue-worker
      image: commatix-queue:latest
      imagePullPolicy: Never  # Use local image, don't pull from registry
```

4. **Apply and restart:**
```bash
kubectl apply -f k8s/queue-workers-deployment.yaml
kubectl rollout restart deployment/commatix-queue-default -n commatix
```

---

## 🔴 Workers Not Processing After Code Changes

**Symptom:** You changed job code in `app/Jobs/`, but workers still running old version.

**Root Cause:** Workers use a Docker image built at a specific point in time. Code changes aren't automatically reflected.

### Solution

Rebuild image and restart workers:

```bash
# 1. Rebuild image with new code
docker build -f Dockerfile.k8s-sail -t commatix-queue:latest .

# 2. Restart workers to use new image
kubectl rollout restart deployment/commatix-queue-default -n commatix
kubectl rollout restart deployment/commatix-queue-campaigns -n commatix

# 3. Watch rollout
kubectl rollout status deployment/commatix-queue-default -n commatix

# 4. Verify new code is active
kubectl logs -f deployment/commatix-queue-default -n commatix
```

**Pro Tip:** Create a script for this:
```bash
#!/bin/bash
# rebuild-workers.sh
docker build -f Dockerfile.k8s-sail -t commatix-queue:latest .
kubectl rollout restart deployment/commatix-queue-default -n commatix
kubectl rollout restart deployment/commatix-queue-campaigns -n commatix
echo "Workers restarted. Watching logs..."
kubectl logs -f deployment/commatix-queue-default -n commatix
```

---

## 🔴 Sail Not Running

**Symptom:** Workers crash because they can't reach MySQL or Redis.

**Root Cause:** Sail containers aren't running, so `host.docker.internal` can't reach them.

### Solution

**Start Sail:**
```bash
./vendor/bin/sail up -d
```

**Verify Sail is running:**
```bash
docker ps | grep commatix
# Should show: commatix-laravel.test-1, commatix-mysql-1, commatix-redis-1, etc.
```

**Verify MySQL is exposed:**
```bash
docker port commatix-mysql-1
# 3306/tcp -> 0.0.0.0:3306
```

**Verify Redis is exposed:**
```bash
docker port commatix-redis-1
# 6379/tcp -> 0.0.0.0:6379
```

**Important:** Workers **require** Sail to be running. This is a hybrid architecture:
- Sail provides: Laravel app, MySQL, Redis, Mailpit
- Kubernetes provides: Queue workers

---

## 🔴 Port Conflicts

**Symptom:**
```bash
./vendor/bin/sail up
# Error: bind: address already in use (port 3306, 6379, or 80)
```

### Solution

**Find what's using the port:**
```bash
# Check port 3306 (MySQL)
sudo lsof -i :3306

# Check port 6379 (Redis)
sudo lsof -i :6379

# Check port 80 (HTTP)
sudo lsof -i :80
```

**Stop conflicting service:**
```bash
# If Apache is using port 80
sudo systemctl stop apache2
sudo systemctl disable apache2

# If another MySQL is running
sudo systemctl stop mysql

# Kill specific process
sudo kill -9 <PID>
```

---

## 🔧 Useful Debugging Commands

### Check Everything

```bash
# Sail status
docker ps --filter "name=commatix"

# Kubernetes pods
kubectl get pods -n commatix

# All Kubernetes resources
kubectl get all -n commatix

# Worker logs
kubectl logs -l app=queue-worker -n commatix --tail=50

# Test database from worker
kubectl exec deployment/commatix-queue-default -n commatix -- \
  php artisan tinker --execute="echo 'DB: ' . DB::connection()->getDatabaseName();"

# Test Redis from worker
kubectl exec deployment/commatix-queue-default -n commatix -- \
  php artisan tinker --execute="echo 'Redis: ' . Cache::driver()->getRedis()->ping();"

# Dispatch test job
docker exec commatix-laravel.test-1 php artisan tinker --execute="App\Jobs\TestKubernetesJob::dispatch();"
```

### Full Health Check Script

```bash
#!/bin/bash
echo "=== Sail Status ==="
docker ps --filter "name=commatix" --format "table {{.Names}}\t{{.Status}}"

echo -e "\n=== Kubernetes Pods ==="
kubectl get pods -n commatix

echo -e "\n=== MySQL Connection (from K8s) ==="
kubectl exec deployment/commatix-queue-default -n commatix -- \
  php artisan tinker --execute="try { echo 'MySQL: Connected (' . DB::connection()->getDatabaseName() . ')'; } catch(Exception \$e) { echo 'MySQL: FAILED - ' . \$e->getMessage(); }"

echo -e "\n=== Redis Connection (from K8s) ==="
kubectl exec deployment/commatix-queue-default -n commatix -- \
  php artisan tinker --execute="try { Cache::driver()->getRedis()->ping(); echo 'Redis: Connected'; } catch(Exception \$e) { echo 'Redis: FAILED - ' . \$e->getMessage(); }"

echo -e "\n=== Recent Worker Logs ==="
kubectl logs deployment/commatix-queue-default -n commatix --tail=10
```

---

## 📞 Still Having Issues?

If you're still stuck:

1. **Check Telescope:** http://localhost/telescope - Look for failed jobs and exceptions
2. **Review Logs:** `kubectl logs -l app=queue-worker -n commatix --tail=100`
3. **Describe Pod:** `kubectl describe pod <pod-name> -n commatix` - Shows events and errors
4. **Exec into Pod:** `kubectl exec -it <pod-name> -n commatix -- /bin/bash` - Debug interactively
5. **Check Git History:** `git log k8s/` - Review recent changes to K8s configs

---

**Last Updated:** October 25, 2025
**Commatix Version:** 1.0
**Kubernetes:** Docker Desktop (local development)
