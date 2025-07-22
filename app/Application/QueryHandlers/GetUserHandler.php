<?php

declare(strict_types=1);

namespace App\Application\QueryHandlers;

use App\Application\Queries\GetUserQuery;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Entities\User;

class GetUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function handle(GetUserQuery $query): ?User
    {
        return $this->userRepository->findById($query->userId);
    }
}