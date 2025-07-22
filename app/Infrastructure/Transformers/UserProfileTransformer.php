<?php

namespace App\Infrastructure\Transformers;

use App\Infrastructure\Models\EloquentUserProfile;
use League\Fractal\TransformerAbstract;

class UserProfileTransformer extends TransformerAbstract
{
    /**
     * Transform the UserProfile entity
     */
    public function transform(EloquentUserProfile $profile): array
    {
        return [
            'id' => $profile->id,
            'user_id' => $profile->user_id,
            'avatar_url' => $profile->avatar_url,
            'bio' => $profile->bio,
            'preferences' => $profile->preferences,
            'settings' => $profile->settings,
            'created_at' => $profile->created_at->toISOString(),
            'updated_at' => $profile->updated_at->toISOString(),
        ];
    }
}