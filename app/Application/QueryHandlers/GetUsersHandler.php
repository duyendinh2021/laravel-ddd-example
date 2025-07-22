<?php

declare(strict_types=1);

namespace App\Application\QueryHandlers;

use App\Application\Queries\GetUsersQuery;
use App\Domain\User\Contracts\UserRepositoryInterface;

class GetUsersHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function handle(GetUsersQuery $query): array
    {
        if ($query->role) {
            return $this->userRepository->findByRole($query->role);
        }

        if ($query->isActive !== null && $query->isActive) {
            return $this->userRepository->findActive();
        }

        return $this->userRepository->findAll();
    }
}