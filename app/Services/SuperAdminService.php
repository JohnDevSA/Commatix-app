<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperAdminService
{
    public function canAccessTenant(string $tenantId): bool
    {
        $user = Auth::user();
        $superAdminType = UserType::where('name', 'Super Admin')->first();
        
        return $user && $user->userType?->id === $superAdminType->id;
    }

    public function switchToTenant(string $tenantId): bool
    {
        if (!$this->canAccessTenant($tenantId)) {
            return false;
        }

        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            return false;
        }

        tenancy()->initialize($tenant);
        session(['current_tenant_id' => $tenantId]);
        
        return true;
    }

    public function exitTenantContext(): void
    {
        tenancy()->end();
        session()->forget('current_tenant_id');
    }

    public function getAllTenants(): Collection
    {
        return Tenant::with(['users', 'domains'])
            ->orderBy('name')
            ->get();
    }

    public function getTenantStats(): array
    {
        $tenants = $this->getAllTenants();
        
        return [
            'total_tenants' => $tenants->count(),
            'active_tenants' => $tenants->where('status', 'active')->count(),
            'trial_tenants' => $tenants->where('status', 'trial')->count(),
            'suspended_tenants' => $tenants->where('status', 'suspended')->count(),
            'total_users' => $tenants->sum(function ($tenant) {
                return $tenant->users->count();
            }),
            'verified_tenants' => $tenants->where('is_verified', true)->count(),
        ];
    }

    public function impersonateUser(string $tenantId, int $userId): ?User
    {
        if (!$this->canAccessTenant($tenantId)) {
            return null;
        }

        $this->switchToTenant($tenantId);
        
        $user = User::where('tenant_id', $tenantId)->find($userId);
        
        if ($user) {
            session(['impersonating_user_id' => $userId, 'original_user_id' => Auth::id()]);
            Auth::login($user);
        }

        return $user;
    }

    public function stopImpersonation(): bool
    {
        $originalUserId = session('original_user_id');
        
        if (!$originalUserId) {
            return false;
        }

        $this->exitTenantContext();
        
        $originalUser = User::find($originalUserId);
        if ($originalUser) {
            Auth::login($originalUser);
            session()->forget(['impersonating_user_id', 'original_user_id']);
            return true;
        }

        return false;
    }

    public function createTenant(array $data): Tenant
    {
        $tenant = Tenant::create($data);
        
        // Create default domain
        if (isset($data['domain'])) {
            $tenant->domains()->create([
                'domain' => $data['domain'],
            ]);
        }

        return $tenant;
    }

    public function updateProviderSettings(string $provider, array $settings): bool
    {
        // Update global provider configurations
        // This would integrate with your provider management system
        return true;
    }
}