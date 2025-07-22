<?php

declare(strict_types=1);

namespace App\Domain\User\Contracts;

use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Email;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    
    public function findByEmail(Email $email): ?User;
    
    public function findByUsername(string $username): ?User;
    
    public function save(User $user): User;
    
    public function deleteUser(int $id): bool;
    
    public function findAll(): array;
    
    public function findByRole(string $role): array;
    
    public function findActive(): array;
    
    public function existsByEmail(Email $email): bool;
    
    public function existsByUsername(string $username): bool;
}