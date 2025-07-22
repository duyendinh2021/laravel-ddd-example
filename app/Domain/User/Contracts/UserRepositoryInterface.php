<?php

declare(strict_types=1);

namespace App\Domain\User\Contracts;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;
use App\Domain\User\ValueObjects\Role;
use App\Domain\Shared\ValueObjects\Id;
use Illuminate\Support\Collection;
use Prettus\Repository\Contracts\RepositoryInterface;

interface UserRepositoryInterface extends RepositoryInterface
{
    // Original interface methods for backward compatibility
    public function findById(int $id): ?User;
    
    public function findByEmail(Email $email): ?User;
    
    public function findByUsername(string $username): ?User;
    
    public function save(User $user): User;
    
    public function deleteUserById(int $id): bool;
    
    public function findAll(): array;
    
    public function findByRoleString(string $role): array;
    
    public function findActive(): array;
    
    public function existsByEmail(Email $email): bool;
    
    public function existsByUsername(string $username): bool;

    /**
     * Domain-specific methods as per requirements
     */
    public function findByRole(Role $role): Collection;
    public function existsWithEmail(Email $email): bool;
    public function findActiveUsers(): Collection;
    public function findRecentUsers(int $days = 30): Collection;
    public function nextIdentity(): Id;
    
    /**
     * Business operations with domain entities
     */
    public function saveUser(User $user): User;
    public function deleteUser(User $user): bool;
    
    /**
     * Query with criteria
     */
    public function getActiveUsersByRole(Role $role): Collection;
    public function getUsersWithProfile(): Collection;
    
    /**
     * Cache management
     */
    public function clearUserCache(string $userId): void;
    public function cacheUser(User $user): void;
}