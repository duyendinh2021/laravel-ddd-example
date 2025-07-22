<?php

declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Commands\UpdateUserCommand;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Entities\User;
use App\Domain\User\ValueObjects\Phone;
use InvalidArgumentException;

class UpdateUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function handle(UpdateUserCommand $command): User
    {
        $user = $this->userRepository->findById($command->userId);
        
        if (!$user) {
            throw new InvalidArgumentException('User not found');
        }

        $phone = $command->phone ? new Phone($command->phone) : null;

        $user->updateProfile(
            firstName: $command->firstName,
            lastName: $command->lastName,
            phone: $phone,
            profileImageUrl: $command->profileImageUrl,
            timezone: $command->timezone,
            language: $command->language
        );

        return $this->userRepository->save($user);
    }
}