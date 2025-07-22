<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\User\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    public function __construct(private User $user)
    {
        parent::__construct($user);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user->getUserId(),
            'username' => $this->user->getUsername(),
            'email' => $this->user->getEmail()->value(),
            'first_name' => $this->user->getFirstName(),
            'last_name' => $this->user->getLastName(),
            'full_name' => $this->user->getFullName(),
            'phone' => $this->user->getPhone()?->value(),
            'is_active' => $this->user->isActive(),
            'is_verified' => $this->user->isVerified(),
            'email_verified_at' => $this->user->getEmailVerifiedAt()?->format('Y-m-d H:i:s'),
            'role' => $this->user->getRole()->value,
            'profile_image_url' => $this->user->getProfileImageUrl(),
            'timezone' => $this->user->getTimezone(),
            'language' => $this->user->getLanguage(),
            'last_login_at' => $this->user->getLastLoginAt()?->format('Y-m-d H:i:s'),
            'created_at' => $this->user->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $this->user->getUpdatedAt()->format('Y-m-d H:i:s'),
            'permissions' => [
                'can_admin' => $this->user->isAdmin(),
                'has_read_permission' => $this->user->hasPermission('read'),
                'has_write_permission' => $this->user->hasPermission('write_own'),
                'can_login' => $this->user->canLogin(),
            ],
        ];
    }
}