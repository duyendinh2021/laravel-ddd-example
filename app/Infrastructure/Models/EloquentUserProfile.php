<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EloquentUserProfile extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'user_profiles';
    
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id',
        'user_id',
        'avatar_url',
        'bio',
        'preferences',
        'settings'
    ];
    
    protected $casts = [
        'preferences' => 'json',
        'settings' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(EloquentUser::class, 'user_id', 'id');
    }
}