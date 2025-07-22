<?php

namespace App\Infrastructure\External;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    private array $config;
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'slack_webhook' => config('services.slack.webhook'),
            'discord_webhook' => config('services.discord.webhook'),
            'enabled' => config('notifications.enabled', true),
        ], $config);
    }
    
    public function sendSlackNotification(string $message, array $data = []): bool
    {
        if (!$this->config['enabled'] || !$this->config['slack_webhook']) {
            return false;
        }
        
        try {
            $payload = [
                'text' => $message,
                'attachments' => [
                    [
                        'color' => 'good',
                        'fields' => array_map(function($key, $value) {
                            return [
                                'title' => $key,
                                'value' => $value,
                                'short' => true
                            ];
                        }, array_keys($data), array_values($data))
                    ]
                ]
            ];
            
            $response = Http::post($this->config['slack_webhook'], $payload);
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to send Slack notification', [
                'message' => $message,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    public function sendUserRegistrationNotification(string $userEmail, string $userName): bool
    {
        $message = "New user registered: {$userName}";
        $data = [
            'Email' => $userEmail,
            'Name' => $userName,
            'Time' => now()->toDateTimeString()
        ];
        
        return $this->sendSlackNotification($message, $data);
    }
    
    public function sendUserLoginNotification(string $userEmail, string $ipAddress): bool
    {
        $message = "User logged in: {$userEmail}";
        $data = [
            'Email' => $userEmail,
            'IP Address' => $ipAddress,
            'Time' => now()->toDateTimeString()
        ];
        
        return $this->sendSlackNotification($message, $data);
    }
    
    public function sendSystemAlert(string $level, string $message, array $context = []): bool
    {
        $alertMessage = "[{$level}] {$message}";
        
        return $this->sendSlackNotification($alertMessage, $context);
    }
}