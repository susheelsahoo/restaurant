<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Throwable;

class PurchaseRoleService
{
    public const ROLE_REQUESTER = 'requester';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_PURCHASING_MANAGER = 'purchasing manager';

    public const PERMISSION_CREATE_REQUESTS = 'create purchase requests';
    public const PERMISSION_VIEW_DEPARTMENT_REQUESTS = 'view department purchase requests';
    public const PERMISSION_APPROVE_REQUESTS = 'approve purchase requests';
    public const PERMISSION_MANAGE_PURCHASE_ORDERS = 'manage purchase orders';

    public function canCreateRequests(?User $user): bool
    {
        return $this->hasPurchasePermission($user, self::PERMISSION_CREATE_REQUESTS);
    }

    public function canApproveRequests(?User $user): bool
    {
        return $this->hasPurchasePermission($user, self::PERMISSION_APPROVE_REQUESTS);
    }

    public function canManagePurchaseOrders(?User $user): bool
    {
        return $this->hasPurchasePermission($user, self::PERMISSION_MANAGE_PURCHASE_ORDERS);
    }

    public function applyRequestVisibility(Builder $query, ?User $user): Builder
    {
        if (! $user || $this->isAdministrator($user)) {
            return $query;
        }

        if ($this->canManagePurchaseOrders($user)) {
            return $query->whereIn('status', ['approved', 'ordered']);
        }

        if ($this->canApproveRequests($user)) {
            $query->whereIn('status', ['submitted', 'returned', 'rejected']);

            if ($user->department_id) {
                $query->where('department_id', $user->department_id);
            }

            return $query;
        }

        if ($this->canCreateRequests($user)) {
            if ($user->department_id) {
                return $query->where('department_id', $user->department_id);
            }

            return $query->where('user_id', $user->id);
        }

        return $query->whereRaw('1 = 0');
    }

    public function userCanViewRequest(?User $user, $purchaseRequest): bool
    {
        if (! $user || ! $purchaseRequest) {
            return false;
        }

        if ($this->isAdministrator($user)) {
            return true;
        }

        if ($this->canManagePurchaseOrders($user)) {
            return in_array($purchaseRequest->status, ['approved', 'ordered'], true);
        }

        if ($this->canApproveRequests($user)) {
            return in_array($purchaseRequest->status, ['submitted', 'returned', 'rejected'], true)
                && $this->matchesUserDepartment($user, $purchaseRequest);
        }

        if ($this->canCreateRequests($user)) {
            return $this->matchesUserDepartment($user, $purchaseRequest)
                || (int) $purchaseRequest->user_id === (int) $user->id;
        }

        return false;
    }

    public function departmentIdForNewRequest(?User $user): ?int
    {
        return $user?->department_id;
    }

    private function hasPurchasePermission(?User $user, string $permission): bool
    {
        if (! $user) {
            return false;
        }

        if ($this->isAdministrator($user)) {
            return true;
        }

        return $this->can($user, $permission);
    }

    private function isAdministrator(User $user): bool
    {
        return $this->hasRole($user, ['administrator']);
    }

    private function hasRole(User $user, array $roles): bool
    {
        if (! $this->permissionTablesExist()) {
            return false;
        }

        try {
            return $user->hasRole($roles)
                || $user->hasRole($roles, 'web')
                || $user->hasRole($roles, 'mobile');
        } catch (Throwable) {
            return false;
        }
    }

    private function can(User $user, string $permission): bool
    {
        if (! $this->permissionTablesExist()) {
            return true;
        }

        try {
            return $user->can($permission)
                || $user->hasPermissionTo($permission, 'web')
                || $user->hasPermissionTo($permission, 'mobile');
        } catch (Throwable) {
            return false;
        }
    }

    private function matchesUserDepartment(User $user, $purchaseRequest): bool
    {
        if (! $user->department_id) {
            return true;
        }

        return (int) $purchaseRequest->department_id === (int) $user->department_id;
    }

    private function permissionTablesExist(): bool
    {
        return Schema::hasTable('roles')
            && Schema::hasTable('permissions')
            && Schema::hasTable('model_has_roles')
            && Schema::hasTable('role_has_permissions');
    }
}
