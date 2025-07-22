<?php

namespace App\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EloquentUser extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'users';
    
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'id',
        'email',
        'password_hash',
        'first_name', 
        'last_name',
        'phone',
        'role',
        'status',
        'email_verified_at',
        'last_login_at'
    ];
    
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * Relationship with profile
     */
    public function profile()
    {
        return $this->hasOne(EloquentUserProfile::class, 'user_id', 'id');
    }
}