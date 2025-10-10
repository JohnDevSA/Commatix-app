<?php

namespace App\Services\Authorization;

use App\Contracts\Services\AuthorizationServiceInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorization Service
 *
 * Centralizes all authorization logic for the application.
 * This service implements the Single Responsibility Principle by handling
 * only authorization concerns, separated from the User model.
 */
class AuthorizationService implements AuthorizationServiceInterface
{
    /**
     * Resource access mapping
     *
     * Maps resource names to the permission check method
     */
    private const RESOURCE_PERMISSIONS = [
        // Super Admin Only Resources
        'tenants' => 'canAccessGlobalResources',
        'document_types' => 'canAccessGlobalResources',
        'user_types' => 'canAccessGlobalResources',
        'industries' => 'canAccessGlobalResources',
        'global_workflows' => 'canAccessGlobalResources',
        'milestones' => 'canAccessGlobalResources',
        'subscriptions' => 'canAccessGlobalResources',
        'usage_monitoring' => 'canAccessGlobalResources',

        // Tenant Admin + Super Admin Resources
        'users' => 'canManageUsers',
        'workflows' => 'canManageWorkflows',
        'tasks' => 'canViewTenantData',
    ];

    /**
     * {@inheritDoc}
     */
    public function canAccessResource(User $user, string $resource): bool
    {
        $permission = self::RESOURCE_PERMISSIONS[$resource] ?? null;

        if (! $permission) {
            return false;
        }

        return $this->$permission($user);
    }

    /**
     * {@inheritDoc}
     */
    public function canPerformAction(User $user, string $action, Model $model): bool
    {
        // Super admins can do everything
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Check tenant ownership for non-super admins
        if (property_exists($model, 'tenant_id') && $model->tenant_id !== null) {
            if ($model->tenant_id !== $user->tenant_id) {
                return false;
            }
        }

        // Action-specific checks
        return match ($action) {
            'view' => $this->canView($user, $model),
            'create' => $this->canCreate($user, $model),
            'update' => $this->canUpdate($user, $model),
            'delete' => $this->canDelete($user, $model),
            default => false,
        };
    }

    /**
     * {@inheritDoc}
     */
    public function applyScopedQuery(User $user, Builder $query): Builder
    {
        // Super admins see everything
        if ($user->isSuperAdmin()) {
            return $query;
        }

        // Other users only see their tenant's data
        return $query->where('tenant_id', $user->tenant_id);
    }

    /**
     * {@inheritDoc}
     */
    public function canManageUsers(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isTenantAdmin();
    }

    /**
     * {@inheritDoc}
     */
    public function canManageWorkflows(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isTenantAdmin();
    }

    /**
     * {@inheritDoc}
     */
    public function canAccessGlobalResources(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * {@inheritDoc}
     */
    public function canViewTenantData(User $user, string $tenantId): bool
    {
        return $user->isSuperAdmin() || $user->tenant_id === $tenantId;
    }

    /**
     * {@inheritDoc}
     */
    public function canImpersonate(User $impersonator, User $target): bool
    {
        // Only super admins can impersonate
        if (! $impersonator->isSuperAdmin()) {
            return false;
        }

        // Cannot impersonate other super admins
        if ($target->isSuperAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can view a model.
     */
    private function canView(User $user, Model $model): bool
    {
        // Tenant users can view records in their tenant
        if (property_exists($model, 'tenant_id')) {
            return $user->tenant_id === $model->tenant_id;
        }

        return false;
    }

    /**
     * Check if user can create a model.
     */
    private function canCreate(User $user, Model $model): bool
    {
        $modelClass = get_class($model);

        return match ($modelClass) {
            \App\Models\User::class => $this->canManageUsers($user),
            \App\Models\WorkflowTemplate::class => $this->canManageWorkflows($user),
            \App\Models\Task::class => true, // All users can create tasks
            default => false,
        };
    }

    /**
     * Check if user can update a model.
     */
    private function canUpdate(User $user, Model $model): bool
    {
        $modelClass = get_class($model);

        return match ($modelClass) {
            \App\Models\User::class => $this->canManageUsers($user),
            \App\Models\WorkflowTemplate::class => $this->canManageWorkflows($user) && $model->created_by === $user->id,
            \App\Models\Task::class => $user->tenant_id === $model->tenant_id,
            default => false,
        };
    }

    /**
     * Check if user can delete a model.
     */
    private function canDelete(User $user, Model $model): bool
    {
        // Only admins can delete most resources
        if ($user->isTenantUser()) {
            return false;
        }

        return $this->canUpdate($user, $model);
    }
}
