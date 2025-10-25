# Commatix Kubernetes Setup

> **Quick Start Guide for Running Commatix Components in Kubernetes**

## Current Status

✅ **Kubernetes Cluster:** Connected (Docker Desktop)
✅ **Namespace:** `commatix` (active)
✅ **Queue Workers:** 5 pods running (2 default + 3 campaigns)
✅ **Workers Processing Jobs:** Successfully processing from Laravel
⚠️ **K8s Redis:** Deployed but NOT USED (workers use Sail Redis)
📋 **Laravel App:** Running in Sail (development mode)

---

## What's Deployed

### 1. Queue Workers ✅ ACTIVE
- **Default Queue:** 2 pods processing general background jobs
- **Campaign Queue:** 3 pods processing campaign jobs
- **Image:** `commatix-queue:latest` (Sail-based)
- **Status:** ✅ Running and processing jobs
- **Connections:**
  - MySQL: `host.docker.internal:3306` (Sail MySQL)
  - Redis: `host.docker.internal:6379` (Sail Redis)

### 2. K8s Redis ⚠️ NOT USED
- **Service:** `commatix-redis` (ClusterIP)
- **Deployment:** 1 replica
- **Status:** ✅ Running but workers DON'T use it
- **Note:** Can be removed with `kubectl delete -f redis-deployment.yaml`
- **Reason:** Workers use Sail's Redis for shared queue with Laravel

---

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│ Docker Desktop Kubernetes (Local)                      │
│                                                          │
│  ┌────────────────────────────────────────────────┐    │
│  │ Namespace: commatix                            │    │
│  │                                                 │    │
│  │  ✅ Redis (commatix-redis)                     │    │
│  │     └── Pod: Running                           │    │
│  │                                                 │    │
│  │  🔄 Queue Workers (pending)                    │    │
│  │     ├── default queue (2 replicas)             │    │
│  │     └── campaigns queue (3 replicas)           │    │
│  │                                                 │    │
│  └────────────────────────────────────────────────┘    │
│                                                          │
│  ┌────────────────────────────────────────────────┐    │
│  │ Docker Compose (Sail) - Still Running          │    │
│  │                                                 │    │
│  │  📦 Laravel App (commatix-laravel.test-1)      │    │
│  │  📦 MySQL (commatix-mysql-1)                   │    │
│  │  📦 Mailpit (commatix-mailpit-1)               │    │
│  │                                                 │    │
│  └────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────┘
```

**Hybrid Setup (FINAL):**
- **Laravel App:** Running in Sail (faster development iteration)
- **MySQL:** Running in Sail (shared database)
- **Redis:** Running in Sail (shared queue - workers connect via `host.docker.internal`)
- **Queue Workers:** Running in Kubernetes (production-like scaling)
- **K8s Redis:** Optional, not used (can be removed)

---

## Files Created

```
k8s/
├── README.md                          # This file (operations guide)
├── QUICK-START.md                     # Command reference
├── SETUP-SUMMARY.md                   # Setup walkthrough & lessons learned
├── TROUBLESHOOTING.md                 # Common issues & solutions
├── redis-deployment.yaml              # K8s Redis (optional, not used)
└── queue-workers-deployment.yaml      # Queue worker deployments ✅

Dockerfile.k8s-sail                    # Working Sail-based image ✅
Dockerfile.k8s                         # Failed slim image attempt
```

---

## Quick Commands

### Check Everything

```bash
# Current namespace
kubectl config current-context
kubectl config get-contexts

# All resources in commatix namespace
kubectl get all -n commatix

# Pod status
kubectl get pods -n commatix
kubectl get pods -n commatix -o wide

# Services
kubectl get svc -n commatix
```

### Redis

```bash
# Check Redis status
kubectl get pods -l app=redis -n commatix

# View Redis logs
kubectl logs -f deployment/commatix-redis -n commatix

# Test Redis connection
kubectl exec -it deployment/commatix-redis -n commatix -- redis-cli ping
# Should return: PONG

# Connect to Redis CLI
kubectl exec -it deployment/commatix-redis -n commatix -- redis-cli
```

### Queue Workers (Once Deployed)

```bash
# Check worker status
kubectl get pods -l app=queue-worker -n commatix

# View logs for default queue
kubectl logs -f deployment/commatix-queue-default -n commatix

# View logs for campaign queue
kubectl logs -f deployment/commatix-queue-campaigns -n commatix

# Scale workers
kubectl scale deployment/commatix-queue-default --replicas=5 -n commatix
kubectl scale deployment/commatix-queue-campaigns --replicas=10 -n commatix

# Restart workers
kubectl rollout restart deployment/commatix-queue-default -n commatix
kubectl rollout restart deployment/commatix-queue-campaigns -n commatix
```

### Secrets & ConfigMaps

```bash
# List secrets
kubectl get secrets -n commatix

# View secret (base64 encoded)
kubectl get secret commatix-app -n commatix -o yaml

# List configmaps
kubectl get configmaps -n commatix
```

### Debugging

```bash
# Describe pod (shows events and issues)
kubectl describe pod <pod-name> -n commatix

# View logs from crashed pod
kubectl logs <pod-name> -n commatix --previous

# Shell into running pod
kubectl exec -it <pod-name> -n commatix -- /bin/sh

# Port forward to access service locally
kubectl port-forward svc/commatix-redis 6379:6379 -n commatix
# Then connect with: redis-cli -h localhost -p 6379
```

### Resource Monitoring

```bash
# Pod resource usage
kubectl top pods -n commatix

# Node resource usage
kubectl top nodes

# Watch pod status in real-time
kubectl get pods -n commatix -w
```

---

## Configuration

### Environment Variables

Queue workers connect to:
- **MySQL:** `commatix-mysql-1` (Sail container)
- **Redis:** `commatix-redis` (K8s service)

Environment variables are set in `queue-workers-deployment.yaml`:

```yaml
env:
  - name: DB_HOST
    value: "commatix-mysql-1"
  - name: REDIS_HOST
    value: "commatix-redis"
  - name: APP_KEY
    valueFrom:
      secretKeyRef:
        name: commatix-app
        key: app-key
```

### Secrets

Created secrets:
- `commatix-app`: Laravel APP_KEY
- `commatix-db`: Database password

### Resource Limits

**Redis:**
- Requests: 256Mi memory, 250m CPU
- Limits: 512Mi memory, 500m CPU

**Default Queue Workers:**
- Requests: 256Mi memory, 250m CPU
- Limits: 512Mi memory, 500m CPU

**Campaign Queue Workers:**
- Requests: 512Mi memory, 500m CPU
- Limits: 1Gi memory, 1000m CPU

---

## Testing the Setup

### 1. Test Redis Connection

```bash
# From Laravel app (Sail)
docker exec commatix-laravel.test-1 php artisan tinker

# In Tinker:
Redis::ping();  // Should return: true
Redis::set('test', 'hello');
Redis::get('test');  // Should return: "hello"
```

### 2. Dispatch a Test Job

```bash
# From Laravel app
docker exec commatix-laravel.test-1 php artisan tinker

# In Tinker:
dispatch(new \App\Jobs\ProcessCampaignJob(\App\Models\Campaign::first()));
```

### 3. Watch Queue Worker Process It

```bash
# Watch queue worker logs
kubectl logs -f deployment/commatix-queue-campaigns -n commatix
```

---

## Troubleshooting

### Redis Not Accessible

```bash
# Check Redis pod status
kubectl get pods -l app=redis -n commatix

# Check service
kubectl get svc commatix-redis -n commatix

# Test connectivity from worker pod (once deployed)
kubectl exec -it deployment/commatix-queue-default -n commatix -- ping commatix-redis
```

### Queue Workers Crash

```bash
# Check logs
kubectl logs <pod-name> -n commatix --previous

# Common issues:
# - APP_KEY not set
# - Can't connect to MySQL
# - Can't connect to Redis
# - Missing PHP extensions

# Describe pod for events
kubectl describe pod <pod-name> -n commatix
```

### Image Pull Errors

```bash
# We're using local images (imagePullPolicy: Never)
# Make sure the image is built:
docker images | grep commatix-queue

# If not found, rebuild:
docker build -f Dockerfile.k8s -t commatix-queue:latest .
```

### Database Connection Errors

The queue workers need to reach the Sail MySQL container. Make sure:

1. Sail is running:
   ```bash
   docker ps | grep commatix-mysql
   ```

2. MySQL container is on same Docker network:
   ```bash
   docker network ls
   docker network inspect bridge
   ```

---

## Next Steps

### Short-term

1. ✅ Deploy queue workers once image is built
2. Test job processing from Laravel to K8s workers
3. Monitor resource usage and adjust limits
4. Set up horizontal pod autoscaling for campaign workers

### Medium-term

1. Deploy Laravel app to K8s (optional)
2. Create Helm chart for easier deployment
3. Add Prometheus metrics for monitoring
4. Implement health check endpoints

### Long-term

1. Deploy to cloud Kubernetes (EKS/GKE/AKS)
2. Multi-region deployment
3. Service mesh (Istio) for advanced traffic management
4. GitOps workflow with ArgoCD

---

## Useful Links

- **Full Documentation:** `/KUBERNETES.md`
- **Kubernetes Docs:** https://kubernetes.io/docs/
- **kubectl Cheat Sheet:** https://kubernetes.io/docs/reference/kubectl/cheatsheet/
- **k9s (Terminal UI):** https://k9scli.io/

---

## Commands to Deploy (Once Image is Built)

```bash
# Deploy queue workers
kubectl apply -f k8s/queue-workers-deployment.yaml

# Watch deployment
kubectl get pods -n commatix -w

# Check logs
kubectl logs -f deployment/commatix-queue-default -n commatix
kubectl logs -f deployment/commatix-queue-campaigns -n commatix
```

---

**Last Updated:** October 25, 2025
**Maintained By:** Commatix DevOps Team
