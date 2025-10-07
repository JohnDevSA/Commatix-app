<?php

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Authorization Service Interface
 *
 * Provides a consistent interface for authorization checks across the application.
 * This service centralizes all authorization logic following the Single Responsibility Principle.
 *
 * @package App\Contracts\Services
 */
interface AuthorizationServiceInterface
{
    /**
     * Check if a user can access a specific resource type.
     *
     * @param User $user The user to check permissions for
     * @param string $resource The resource type (e.g., 'tenants', 'users', 'workflows')
     * @return bool True if the user can access the resource
     */
    public function canAccessResource(User $user, string $resource): bool;

    /**
     * Check if a user can perform a specific action on a model.
     *
     * @param User $user The user to check permissions for
     * @param string $action The action to perform (e.g., 'view', 'create', 'update', 'delete')
     * @param Model $model The model instance to perform the action on
     * @return bool True if the user can perform the action
     */
    public function canPerformAction(User $user, string $action, Model $model): bool;

    /**
     * Apply tenant scoping to a query based on user permissions.
     *
     * Super Admins see all records. Other users see only their tenant's records.
     *
     * @param User $user The user to scope the query for
     * @param Builder $query The query builder to scope
     * @return Builder The scoped query
     */
    public function applyScopedQuery(User $user, Builder $query): Builder;

    /**
     * Check if a user can manage other users.
     *
     * @param User $user The user to check permissions for
     * @return bool True if the user can manage users
     */
    public function canManageUsers(User $user): bool;

    /**
     * Check if a user can manage workflows.
     *
     * @param User $user The user to check permissions for
     * @return bool True if the user can manage workflows
     */
    public function canManageWorkflows(User $user): bool;

    /**
     * Check if a user can access global/system resources.
     *
     * @param User $user The user to check permissions for
     * @return bool True if the user can access global resources
     */
    public function canAccessGlobalResources(User $user): bool;

    /**
     * Check if a user can view data for a specific tenant.
     *
     * @param User $user The user to check permissions for
     * @param string $tenantId The tenant ID to check access for
     * @return bool True if the user can view the tenant's data
     */
    public function canViewTenantData(User $user, string $tenantId): bool;

    /**
     * Check if a user can impersonate another user.
     *
     * @param User $impersonator The user attempting to impersonate
     * @param User $target The user to be impersonated
     * @return bool True if impersonation is allowed
     */
    public function canImpersonate(User $impersonator, User $target): bool;
}