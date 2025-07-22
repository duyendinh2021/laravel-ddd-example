<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // Primary key and identification
            $table->id('user_id');
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            
            // Authentication info
            $table->text('password_hash');
            $table->string('password_salt', 32)->nullable();
            
            // Basic info
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('phone', 20)->nullable();
            
            // Account status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            
            // Basic role system
            $table->enum('role', ['admin', 'user', 'guest'])->default('user');
            
            // Time tracking
            $table->timestamps();
            $table->timestamp('last_login_at')->nullable();
            
            // Additional info
            $table->text('profile_image_url')->nullable();
            $table->string('timezone', 50)->default('Asia/Ho_Chi_Minh');
            $table->string('language', 10)->default('vi');
            
            // Indexes
            $table->index('username');
            $table->index('email');
            $table->index(['is_active'], 'idx_users_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
