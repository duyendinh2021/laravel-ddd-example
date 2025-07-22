<?php

namespace App\Infrastructure\Transformers;

use App\Infrastructure\Models\EloquentUser;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * Resources that can be included if requested
     */
    protected array $availableIncludes = ['profile', 'roles'];
    
    /**
     * Resources that are included by default
     */  
    protected array $defaultIncludes = [];
    
    /**
     * Transform the User entity
     */
    public function transform(EloquentUser $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'full_name' => trim($user->first_name . ' ' . $user->last_name),
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'role' => $user->role,
            'status' => $user->status,
            'email_verified' => !is_null($user->email_verified_at),
            'last_login' => $user->last_login_at?->toISOString(),
            'created_at' => $user->created_at->toISOString(),
            'updated_at' => $user->updated_at->toISOString(),
        ];
    }
    
    /**
     * Include Profile relationship
     */
    public function includeProfile(EloquentUser $user): ?\League\Fractal\Resource\Item
    {
        $profile = $user->profile;
        
        if (!$profile) {
            return null;
        }
        
        return $this->item($profile, new UserProfileTransformer());
    }
}