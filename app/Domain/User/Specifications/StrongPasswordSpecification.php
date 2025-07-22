<?php

declare(strict_types=1);

namespace App\Domain\User\Specifications;

use App\Domain\User\ValueObjects\Password;

class StrongPasswordSpecification
{
    public function isSatisfiedBy(Password $password): bool
    {
        return $password->isStrong();
    }
}