# Kubernetes Quick Start for Commatix

## ⚡ Essential Commands

### Check Status
```bash
# What's running?
kubectl get all -n commatix

# Pod status
kubectl get pods -n commatix

# Watch pods in real-time
kubectl get pods -n commatix -w
```

### View Logs
```bash
# Redis logs
kubectl logs -f deployment/commatix-redis -n commatix

# Default queue worker logs
kubectl logs -f deployment/commatix-queue-default -n commatix

# Campaign queue worker logs
kubectl logs -f deployment/commatix-queue-campaigns -n commatix

# All queue worker logs (stream from multiple pods)
kubectl logs -f -l app=queue-worker -n commatix --max-log-requests=10
```

### Scale Workers
```bash
# Scale default queue workers
kubectl scale deployment/commatix-queue-default --replicas=5 -n commatix

# Scale campaign queue workers
kubectl scale deployment/commatix-queue-campaigns --replicas=10 -n commatix

# Auto-scale based on CPU
kubectl autoscale deployment/commatix-queue-campaigns \
  --cpu-percent=80 --min=2 --max=20 -n commatix
```

### Restart/Update
```bash
# Restart Redis
kubectl rollout restart deployment/commatix-redis -n commatix

# Restart all queue workers
kubectl rollout restart deployment/commatix-queue-default -n commatix
kubectl rollout restart deployment/commatix-queue-campaigns -n commatix

# Update image (after rebuilding)
kubectl set image deployment/commatix-queue-default \
  queue-worker=commatix-queue:latest -n commatix
```

### Debug
```bash
# Describe pod (shows events)
kubectl describe pod <pod-name> -n commatix

# Previous logs (from crashed pod)
kubectl logs <pod-name> -n commatix --previous

# Shell into pod
kubectl exec -it <pod-name> -n commatix -- /bin/sh

# Test Redis
kubectl exec -it deployment/commatix-redis -n commatix -- redis-cli ping
```

### Monitoring
```bash
# Resource usage
kubectl top pods -n commatix
kubectl top nodes

# Pod details
kubectl get pods -n commatix -o wide
```

## 🚀 Deploy Queue Workers

```bash
# Build image (if not already built)
docker build -f Dockerfile.k8s -t commatix-queue:latest .

# Deploy
kubectl apply -f k8s/queue-workers-deployment.yaml

# Verify
kubectl get pods -l app=queue-worker -n commatix
kubectl logs -f deployment/commatix-queue-default -n commatix
```

## 🔧 Quick Fixes

### Workers Crashing?
```bash
# Check logs for errors
kubectl logs <pod-name> -n commatix --previous

# Common fixes:
# - Rebuild image if code changed
# - Check DB connection (is Sail MySQL running?)
# - Check Redis connection
# - Verify APP_KEY secret is set
```

### Can't Connect to MySQL?
```bash
# Make sure Sail is running
docker ps | grep mysql

# Queue workers connect to: commatix-mysql-1
# Check if it's reachable
docker network inspect bridge
```

### Redis Not Working?
```bash
# Check Redis pod
kubectl get pods -l app=redis -n commatix

# Check service
kubectl get svc commatix-redis -n commatix

# Test connection
kubectl exec -it deployment/commatix-redis -n commatix -- redis-cli ping
```

## 📊 Test Queue Processing

### 1. From Laravel (Sail)
```bash
docker exec commatix-laravel.test-1 php artisan tinker
```

### 2. Dispatch Test Job
```php
// In Tinker:
dispatch(function() {
    \Log::info('Test job from Kubernetes worker!');
});
```

### 3. Watch Worker Process It
```bash
kubectl logs -f deployment/commatix-queue-default -n commatix
```

## 🎯 What's Next?

1. Monitor worker logs and adjust replicas
2. Test campaign processing
3. Implement auto-scaling
4. Add monitoring with Prometheus

---

**Need Help?** See `k8s/README.md` for detailed documentation
