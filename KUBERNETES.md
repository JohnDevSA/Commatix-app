# Kubernetes Integration Guide for Commatix

> **Status:** Planning & Documentation Phase
> **Last Updated:** October 2025
> **Target:** Docker Desktop Kubernetes (Local Development)

## Table of Contents

- [Overview](#overview)
- [Why Kubernetes for Commatix?](#why-kubernetes-for-commatix)
- [Prerequisites](#prerequisites)
- [Use Cases](#use-cases)
- [Architecture Overview](#architecture-overview)
- [Setup Guide](#setup-guide)
- [Deployment Strategies](#deployment-strategies)
- [Monitoring & Operations](#monitoring--operations)
- [Troubleshooting](#troubleshooting)
- [Production Considerations](#production-considerations)

---

## Overview

This guide documents how to leverage Kubernetes (K8s) in Docker Desktop for Commatix development, testing, and eventual production deployment.

**Current Infrastructure:**
- Docker Desktop with Kubernetes enabled
- Laravel Sail for local development
- MySQL + Redis in Docker containers
- Multi-tenant Laravel 12 application

**Goal:**
Progressively adopt Kubernetes to improve:
- Queue worker management and scaling
- Development/production environment parity
- Infrastructure as Code (IaC) practices
- Production deployment readiness

---

## Why Kubernetes for Commatix?

### Strategic Benefits

1. **Queue Worker Management**
   - Run separate pods for different queue types (campaigns, emails, SMS)
   - Auto-restart failed workers
   - Scale workers based on queue depth
   - Better resource isolation

2. **Multi-Tenant Scalability**
   - Deploy tenant-specific workers if needed
   - Horizontal scaling for high-traffic tenants
   - Resource quotas and limits per tenant

3. **Production Readiness**
   - Learn K8s locally before cloud deployment
   - Test deployments and rollbacks
   - Practice disaster recovery scenarios

4. **Cost Optimization**
   - Efficient resource utilization
   - Auto-scaling reduces waste
   - Better monitoring of resource usage

5. **DevOps Best Practices**
   - Infrastructure as Code (manifests + Helm)
   - GitOps workflows
   - Consistent environments (dev → staging → prod)

---

## Prerequisites

### Required

✅ **Docker Desktop** with Kubernetes enabled
```bash
# Verify Kubernetes is running
kubectl version --short
kubectl cluster-info
```

✅ **kubectl** CLI installed and configured
```bash
# Should show docker-desktop context
kubectl config current-context
```

✅ **Commatix** running via Laravel Sail
```bash
./vendor/bin/sail up -d
```

### Optional but Recommended

- **Helm 3** - Kubernetes package manager
- **k9s** - Terminal UI for Kubernetes
- **Lens** - Kubernetes IDE (GUI)
- **Skaffold** - Local K8s development workflow

### Installation Commands

```bash
# Install Helm (macOS)
brew install helm

# Install k9s (macOS)
brew install k9s

# Install Lens
# Download from: https://k8slens.dev/

# Install Skaffold (macOS)
brew install skaffold
```

---

## Use Cases

### Phase 1: Development & Learning (Current)

**Goal:** Learn Kubernetes without disrupting current workflow

1. **Deploy Queue Workers in K8s**
   - Keep Laravel app in Sail
   - Run queue workers as K8s pods
   - Learn pod management, logs, scaling

2. **Redis in Kubernetes**
   - Deploy Redis using Helm chart
   - Practice StatefulSets and persistent volumes
   - Test failover scenarios

3. **Staging Environment**
   - Full Commatix stack in K8s
   - Test deployments before production
   - Practice CI/CD pipelines

### Phase 2: Hybrid Deployment (Future)

**Goal:** Run production-critical components in K8s

1. **Queue Workers in Production**
   - Campaign processing
   - Email/SMS sending
   - Background jobs

2. **Microservices Extraction**
   - API gateway in K8s
   - Tenant-specific services
   - Analytics/reporting services

### Phase 3: Full Kubernetes (Long-term)

**Goal:** Entire Commatix platform on K8s

1. **Complete Migration**
   - Laravel app as K8s deployment
   - MySQL as StatefulSet or managed service
   - Redis cluster for high availability
   - Load balancing and auto-scaling

---

## Architecture Overview

### Current Architecture (Laravel Sail)

```
┌─────────────────────────────────────────┐
│          Docker Desktop                  │
│                                          │
│  ┌──────────────────────────────────┐  │
│  │  commatix-laravel.test-1         │  │
│  │  (Laravel + PHP-FPM + Nginx)     │  │
│  └──────────────────────────────────┘  │
│                                          │
│  ┌──────────────────────────────────┐  │
│  │  commatix-mysql-1                │  │
│  │  (MySQL 8)                       │  │
│  └──────────────────────────────────┘  │
│                                          │
│  ┌──────────────────────────────────┐  │
│  │  commatix-redis-1                │  │
│  │  (Redis)                         │  │
│  └──────────────────────────────────┘  │
└─────────────────────────────────────────┘
```

### Target Architecture (Kubernetes Hybrid)

```
┌─────────────────────────────────────────────────────────┐
│          Docker Desktop Kubernetes                      │
│                                                          │
│  ┌────────────────────────────────────────────────┐    │
│  │  Namespace: commatix                            │    │
│  │                                                 │    │
│  │  ┌─────────────────────────────────────────┐  │    │
│  │  │  Laravel App Deployment                  │  │    │
│  │  │  Replicas: 2                             │  │    │
│  │  │  - commatix-app-xxxxx (pod 1)            │  │    │
│  │  │  - commatix-app-yyyyy (pod 2)            │  │    │
│  │  └─────────────────────────────────────────┘  │    │
│  │                                                 │    │
│  │  ┌─────────────────────────────────────────┐  │    │
│  │  │  Queue Workers Deployment                │  │    │
│  │  │  - commatix-queue-default (pod)          │  │    │
│  │  │  - commatix-queue-campaigns (pod)        │  │    │
│  │  │  - commatix-queue-emails (pod)           │  │    │
│  │  └─────────────────────────────────────────┘  │    │
│  │                                                 │    │
│  │  ┌─────────────────────────────────────────┐  │    │
│  │  │  MySQL StatefulSet                       │  │    │
│  │  │  - commatix-mysql-0 (pod)                │  │    │
│  │  │  + Persistent Volume (data)              │  │    │
│  │  └─────────────────────────────────────────┘  │    │
│  │                                                 │    │
│  │  ┌─────────────────────────────────────────┐  │    │
│  │  │  Redis Deployment                        │  │    │
│  │  │  - commatix-redis-xxxxx (pod)            │  │    │
│  │  └─────────────────────────────────────────┘  │    │
│  │                                                 │    │
│  │  ┌─────────────────────────────────────────┐  │    │
│  │  │  Services (Load Balancing)               │  │    │
│  │  │  - commatix-app-svc (ClusterIP)          │  │    │
│  │  │  - commatix-mysql-svc (ClusterIP)        │  │    │
│  │  │  - commatix-redis-svc (ClusterIP)        │  │    │
│  │  └─────────────────────────────────────────┘  │    │
│  └────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────┘
```

---

## Setup Guide

### Step 1: Enable Kubernetes in Docker Desktop

1. Open **Docker Desktop**
2. Go to **Settings → Kubernetes**
3. Check **Enable Kubernetes**
4. Click **Apply & Restart**

Verify:
```bash
kubectl cluster-info
# Should show: Kubernetes control plane is running at https://kubernetes.docker.internal:6443
```

### Step 2: Create Commatix Namespace

```bash
# Create namespace for Commatix
kubectl create namespace commatix

# Set as default namespace (optional)
kubectl config set-context --current --namespace=commatix

# Verify
kubectl get namespaces
```

### Step 3: Create ConfigMap for Environment Variables

```bash
# Create from .env file
kubectl create configmap commatix-env \
  --from-env-file=.env \
  --namespace=commatix

# Verify
kubectl get configmap commatix-env -n commatix -o yaml
```

### Step 4: Create Secrets for Sensitive Data

```bash
# Database credentials
kubectl create secret generic commatix-db \
  --from-literal=password=your_db_password \
  --namespace=commatix

# Application key
kubectl create secret generic commatix-app \
  --from-literal=app-key=base64:your_laravel_app_key \
  --namespace=commatix

# Verify
kubectl get secrets -n commatix
```

### Step 5: Deploy MySQL

Create `k8s/mysql-statefulset.yaml`:

```yaml
apiVersion: v1
kind: Service
metadata:
  name: commatix-mysql
  namespace: commatix
spec:
  ports:
    - port: 3306
  selector:
    app: mysql
  clusterIP: None
---
apiVersion: apps/v1
kind: StatefulSet
metadata:
  name: commatix-mysql
  namespace: commatix
spec:
  serviceName: commatix-mysql
  replicas: 1
  selector:
    matchLabels:
      app: mysql
  template:
    metadata:
      labels:
        app: mysql
    spec:
      containers:
        - name: mysql
          image: mysql:8.0
          env:
            - name: MYSQL_ROOT_PASSWORD
              valueFrom:
                secretKeyRef:
                  name: commatix-db
                  key: password
            - name: MYSQL_DATABASE
              value: commatix
          ports:
            - containerPort: 3306
          volumeMounts:
            - name: mysql-data
              mountPath: /var/lib/mysql
  volumeClaimTemplates:
    - metadata:
        name: mysql-data
      spec:
        accessModes: ["ReadWriteOnce"]
        resources:
          requests:
            storage: 10Gi
```

Deploy:
```bash
kubectl apply -f k8s/mysql-statefulset.yaml
kubectl get pods -n commatix -w
```

### Step 6: Deploy Redis

Create `k8s/redis-deployment.yaml`:

```yaml
apiVersion: v1
kind: Service
metadata:
  name: commatix-redis
  namespace: commatix
spec:
  ports:
    - port: 6379
  selector:
    app: redis
---
apiVersion: apps/v1
kind: Deployment
metadata:
  name: commatix-redis
  namespace: commatix
spec:
  replicas: 1
  selector:
    matchLabels:
      app: redis
  template:
    metadata:
      labels:
        app: redis
    spec:
      containers:
        - name: redis
          image: redis:7-alpine
          ports:
            - containerPort: 6379
          resources:
            requests:
              memory: "256Mi"
              cpu: "250m"
            limits:
              memory: "512Mi"
              cpu: "500m"
```

Deploy:
```bash
kubectl apply -f k8s/redis-deployment.yaml
```

### Step 7: Deploy Laravel App

Create `k8s/laravel-deployment.yaml`:

```yaml
apiVersion: v1
kind: Service
metadata:
  name: commatix-app
  namespace: commatix
spec:
  type: LoadBalancer
  ports:
    - port: 80
      targetPort: 8000
  selector:
    app: laravel
---
apiVersion: apps/v1
kind: Deployment
metadata:
  name: commatix-app
  namespace: commatix
spec:
  replicas: 2
  selector:
    matchLabels:
      app: laravel
  template:
    metadata:
      labels:
        app: laravel
    spec:
      containers:
        - name: laravel
          image: commatix:latest  # Build this with Docker
          ports:
            - containerPort: 8000
          envFrom:
            - configMapRef:
                name: commatix-env
          env:
            - name: APP_KEY
              valueFrom:
                secretKeyRef:
                  name: commatix-app
                  key: app-key
            - name: DB_HOST
              value: commatix-mysql
            - name: REDIS_HOST
              value: commatix-redis
          resources:
            requests:
              memory: "512Mi"
              cpu: "500m"
            limits:
              memory: "1Gi"
              cpu: "1000m"
          livenessProbe:
            httpGet:
              path: /health
              port: 8000
            initialDelaySeconds: 30
            periodSeconds: 10
          readinessProbe:
            httpGet:
              path: /health
              port: 8000
            initialDelaySeconds: 5
            periodSeconds: 5
```

Deploy:
```bash
kubectl apply -f k8s/laravel-deployment.yaml
kubectl get svc commatix-app -n commatix
```

### Step 8: Deploy Queue Workers

Create `k8s/queue-workers-deployment.yaml`:

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: commatix-queue-default
  namespace: commatix
spec:
  replicas: 2
  selector:
    matchLabels:
      app: queue-worker
      queue: default
  template:
    metadata:
      labels:
        app: queue-worker
        queue: default
    spec:
      containers:
        - name: queue-worker
          image: commatix:latest
          command: ["php", "artisan", "queue:work", "redis", "--queue=default", "--tries=3", "--timeout=90"]
          envFrom:
            - configMapRef:
                name: commatix-env
          env:
            - name: DB_HOST
              value: commatix-mysql
            - name: REDIS_HOST
              value: commatix-redis
          resources:
            requests:
              memory: "256Mi"
              cpu: "250m"
            limits:
              memory: "512Mi"
              cpu: "500m"
---
apiVersion: apps/v1
kind: Deployment
metadata:
  name: commatix-queue-campaigns
  namespace: commatix
spec:
  replicas: 3
  selector:
    matchLabels:
      app: queue-worker
      queue: campaigns
  template:
    metadata:
      labels:
        app: queue-worker
        queue: campaigns
    spec:
      containers:
        - name: queue-worker
          image: commatix:latest
          command: ["php", "artisan", "queue:work", "redis", "--queue=campaigns", "--tries=3", "--timeout=300"]
          envFrom:
            - configMapRef:
                name: commatix-env
          env:
            - name: DB_HOST
              value: commatix-mysql
            - name: REDIS_HOST
              value: commatix-redis
          resources:
            requests:
              memory: "512Mi"
              cpu: "500m"
            limits:
              memory: "1Gi"
              cpu: "1000m"
```

Deploy:
```bash
kubectl apply -f k8s/queue-workers-deployment.yaml
kubectl get pods -n commatix -l app=queue-worker
```

---

## Deployment Strategies

### Local Development Workflow

**Option A: Sail + K8s Hybrid** (Recommended for learning)
- Keep Laravel app in Sail for fast iteration
- Deploy only queue workers to K8s
- Shared MySQL and Redis

**Option B: Full K8s** (Production-like)
- Entire stack in Kubernetes
- Use Skaffold for hot-reload
- Slower iteration but higher fidelity

### Building Docker Image for K8s

Create `Dockerfile.k8s`:

```dockerfile
FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    mysql-client \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 8000

# Start Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000
```

Build and tag:
```bash
docker build -f Dockerfile.k8s -t commatix:latest .
```

### Deploying Updates

```bash
# Update deployment
kubectl set image deployment/commatix-app laravel=commatix:v1.1 -n commatix

# Or apply updated manifest
kubectl apply -f k8s/laravel-deployment.yaml

# Watch rollout
kubectl rollout status deployment/commatix-app -n commatix

# Rollback if needed
kubectl rollout undo deployment/commatix-app -n commatix
```

---

## Monitoring & Operations

### Viewing Logs

```bash
# Laravel app logs
kubectl logs -f deployment/commatix-app -n commatix

# Queue worker logs
kubectl logs -f deployment/commatix-queue-campaigns -n commatix

# All pods with label
kubectl logs -f -l app=queue-worker -n commatix --max-log-requests=10

# Stream logs from specific pod
kubectl logs -f commatix-app-xxxxx -n commatix
```

### Scaling

```bash
# Scale Laravel app
kubectl scale deployment/commatix-app --replicas=5 -n commatix

# Scale queue workers
kubectl scale deployment/commatix-queue-campaigns --replicas=10 -n commatix

# Auto-scaling (HPA)
kubectl autoscale deployment/commatix-queue-campaigns \
  --cpu-percent=80 \
  --min=2 \
  --max=20 \
  -n commatix
```

### Accessing Pods

```bash
# Execute commands in pod
kubectl exec -it commatix-app-xxxxx -n commatix -- php artisan tinker

# Shell into pod
kubectl exec -it commatix-app-xxxxx -n commatix -- /bin/sh

# Run migrations
kubectl exec -it commatix-app-xxxxx -n commatix -- php artisan migrate
```

### Resource Monitoring

```bash
# Pod resource usage
kubectl top pods -n commatix

# Node resource usage
kubectl top nodes

# Describe pod for events
kubectl describe pod commatix-app-xxxxx -n commatix
```

### Using k9s (Terminal UI)

```bash
# Launch k9s
k9s -n commatix

# Keyboard shortcuts:
# :pods - View pods
# :svc - View services
# :deploy - View deployments
# l - View logs
# d - Describe
# s - Shell into pod
```

---

## Troubleshooting

### Common Issues

**1. Pods stuck in `Pending`**
```bash
# Check events
kubectl describe pod <pod-name> -n commatix

# Common causes:
# - Insufficient resources
# - PVC not binding
# - Image pull errors
```

**2. Pods crashing (CrashLoopBackOff)**
```bash
# View logs
kubectl logs <pod-name> -n commatix --previous

# Common causes:
# - Missing environment variables
# - Database connection errors
# - App key not set
```

**3. Service not accessible**
```bash
# Check service
kubectl get svc commatix-app -n commatix

# Check endpoints
kubectl get endpoints commatix-app -n commatix

# Port forward for testing
kubectl port-forward svc/commatix-app 8080:80 -n commatix
# Then visit http://localhost:8080
```

**4. Database connection errors**
```bash
# Test MySQL connectivity
kubectl exec -it commatix-app-xxxxx -n commatix -- \
  mysql -h commatix-mysql -u root -p

# Check MySQL pod status
kubectl logs commatix-mysql-0 -n commatix
```

**5. Queue jobs not processing**
```bash
# Check queue worker logs
kubectl logs -f deployment/commatix-queue-default -n commatix

# Check Redis connection
kubectl exec -it commatix-app-xxxxx -n commatix -- \
  redis-cli -h commatix-redis ping
```

---

## Production Considerations

### Security

- **Use secrets management** - Kubernetes Secrets or external (Vault, AWS Secrets Manager)
- **RBAC** - Role-Based Access Control for team members
- **Network Policies** - Restrict pod-to-pod communication
- **Pod Security Standards** - Enforce security best practices
- **Image scanning** - Scan Docker images for vulnerabilities

### High Availability

- **Multi-replica deployments** - Laravel app, queue workers
- **Pod Disruption Budgets** - Prevent too many pods going down
- **Health checks** - Liveness and readiness probes
- **Database HA** - MySQL cluster or managed service (RDS, CloudSQL)
- **Redis HA** - Redis Sentinel or cluster mode

### Backup & Disaster Recovery

- **MySQL backups** - Automated PVC snapshots or mysqldump
- **Configuration backups** - Version control all K8s manifests
- **Disaster recovery plan** - Document restore procedures
- **Test restores regularly** - Ensure backups actually work

### Monitoring & Observability

- **Prometheus + Grafana** - Metrics and dashboards
- **ELK or Loki** - Log aggregation
- **Jaeger** - Distributed tracing
- **Alerting** - PagerDuty, Slack notifications

### Cost Optimization

- **Resource requests/limits** - Set appropriate CPU/memory
- **Horizontal Pod Autoscaling** - Scale based on load
- **Cluster autoscaling** - Add/remove nodes automatically
- **Spot instances** - Use cheaper compute for non-critical workloads

### Cloud Migration Path

When ready for production cloud deployment:

1. **AWS EKS** - Elastic Kubernetes Service
2. **Google GKE** - Google Kubernetes Engine
3. **Azure AKS** - Azure Kubernetes Service

Migration steps:
- Export K8s manifests from local
- Update image registry (ECR, GCR, ACR)
- Update LoadBalancer to cloud provider's
- Configure cloud-specific storage classes
- Set up cloud monitoring integrations

---

## Next Steps

### Immediate (Learning Phase)

1. ✅ Enable Kubernetes in Docker Desktop
2. ✅ Create `commatix` namespace
3. ✅ Deploy MySQL StatefulSet
4. ✅ Deploy Redis
5. ✅ Deploy queue workers

### Short-term (1-2 weeks)

1. Create Helm chart for Commatix
2. Set up Skaffold for local development
3. Implement health check endpoints (`/health`, `/ready`)
4. Create `/k8s` specialist command
5. Document common operations

### Medium-term (1-2 months)

1. Deploy full Commatix stack to K8s
2. Implement auto-scaling for queue workers
3. Set up monitoring (Prometheus + Grafana)
4. Create CI/CD pipeline for K8s deployments
5. Test disaster recovery procedures

### Long-term (3-6 months)

1. Production deployment to cloud K8s (EKS/GKE/AKS)
2. Multi-region deployment
3. Advanced features (service mesh, GitOps)
4. Extract microservices where beneficial

---

## Resources

### Official Documentation
- [Kubernetes Docs](https://kubernetes.io/docs/)
- [Laravel Deployment](https://laravel.com/docs/12.x/deployment)
- [Helm Documentation](https://helm.sh/docs/)

### Tutorials
- [Kubernetes Basics](https://kubernetes.io/docs/tutorials/kubernetes-basics/)
- [Laravel on Kubernetes](https://learnk8s.io/deploying-laravel-to-kubernetes)

### Tools
- [kubectl Cheat Sheet](https://kubernetes.io/docs/reference/kubectl/cheatsheet/)
- [k9s](https://k9scli.io/)
- [Lens](https://k8slens.dev/)
- [Skaffold](https://skaffold.dev/)

---

**Document Maintainer:** Commatix Architecture Team
**Feedback:** Create an issue or PR with suggested improvements
