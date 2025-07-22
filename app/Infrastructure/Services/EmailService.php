<?php

namespace App\Infrastructure\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class EmailService
{
    public function sendEmail(string $to, string $subject, string $body, array $attachments = []): bool
    {
        try {
            // Simple email sending implementation
            // In real application, you would use proper mail templates and configuration
            Mail::raw($body, function ($message) use ($to, $subject, $attachments) {
                $message->to($to)
                       ->subject($subject);
                       
                foreach ($attachments as $attachment) {
                    $message->attach($attachment);
                }
            });
            
            return true;
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to send email', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    public function sendWelcomeEmail(string $email, string $name): bool
    {
        $subject = 'Welcome to Our Platform';
        $body = "Hello {$name},\n\nWelcome to our platform! We're excited to have you on board.";
        
        return $this->sendEmail($email, $subject, $body);
    }
    
    public function sendPasswordResetEmail(string $email, string $resetLink): bool
    {
        $subject = 'Password Reset Request';
        $body = "Hello,\n\nClick the following link to reset your password: {$resetLink}";
        
        return $this->sendEmail($email, $subject, $body);
    }
}