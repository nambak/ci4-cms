<?php

namespace App\Enums;

/**
 * Shield 그룹(AuthGroups.php)과 동일한 값으로 유지
 */
enum UserRole: string
{
    case Superadmin = 'superadmin';
    case Admin      = 'admin';
    case User       = 'user';

    public function label(): string
    {
        return match($this) {
            UserRole::Superadmin => 'Super Admin',
            UserRole::Admin      => 'Admin',
            UserRole::User       => 'User',
        };
    }

    public function hasAdminAccess(): bool
    {
        return in_array($this, [UserRole::Superadmin, UserRole::Admin,], true);
    }
}
