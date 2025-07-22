<?php

namespace App\Infrastructure\External;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AuditService
{
    private bool $enabled;
    private string $table;
    
    public function __construct()
    {
        $this->enabled = config('audit.enabled', true);
        $this->table = config('audit.table', 'audit_logs');
    }
    
    public function log(string $action, string $entity, string $entityId, array $data = [], ?int $userId = null): bool
    {
        if (!$this->enabled) {
            return true;
        }
        
        try {
            DB::table($this->table)->insert([
                'user_id' => $userId,
                'action' => $action,
                'entity' => $entity,
                'entity_id' => $entityId,
                'data' => json_encode($data),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to log audit entry', [
                'action' => $action,
                'entity' => $entity,
                'entity_id' => $entityId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    public function logUserRegistration(string $userId, array $userData): bool
    {
        return $this->log('user.registered', 'User', $userId, [
            'email' => $userData['email'] ?? null,
            'name' => ($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? ''),
        ]);
    }
    
    public function logUserLogin(string $userId, string $email): bool
    {
        return $this->log('user.login', 'User', $userId, [
            'email' => $email,
            'login_time' => now()->toDateTimeString(),
        ], (int)$userId);
    }
    
    public function logUserUpdate(string $userId, array $changes, ?int $updatedBy = null): bool
    {
        return $this->log('user.updated', 'User', $userId, [
            'changes' => $changes,
        ], $updatedBy);
    }
    
    public function logUserDeletion(string $userId, ?int $deletedBy = null): bool
    {
        return $this->log('user.deleted', 'User', $userId, [], $deletedBy);
    }
    
    public function logPasswordChange(string $userId, ?int $changedBy = null): bool
    {
        return $this->log('user.password_changed', 'User', $userId, [
            'changed_at' => now()->toDateTimeString(),
        ], $changedBy);
    }
    
    public function getAuditTrail(string $entityType, string $entityId, int $limit = 50): array
    {
        try {
            return DB::table($this->table)
                ->where('entity', $entityType)
                ->where('entity_id', $entityId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to retrieve audit trail', [
                'entity' => $entityType,
                'entity_id' => $entityId,
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }
}