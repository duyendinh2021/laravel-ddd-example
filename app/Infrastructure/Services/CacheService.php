<?php

namespace App\Infrastructure\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    private string $defaultTtl;
    private array $tags;
    
    public function __construct(int $defaultTtl = 3600)
    {
        $this->defaultTtl = $defaultTtl;
        $this->tags = [];
    }
    
    public function get(string $key, $default = null)
    {
        if (empty($this->tags)) {
            return Cache::get($key, $default);
        }
        
        return Cache::tags($this->tags)->get($key, $default);
    }
    
    public function put(string $key, $value, int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTtl;
        
        if (empty($this->tags)) {
            return Cache::put($key, $value, $ttl);
        }
        
        return Cache::tags($this->tags)->put($key, $value, $ttl);
    }
    
    public function remember(string $key, callable $callback, int $ttl = null)
    {
        $ttl = $ttl ?? $this->defaultTtl;
        
        if (empty($this->tags)) {
            return Cache::remember($key, $ttl, $callback);
        }
        
        return Cache::tags($this->tags)->remember($key, $ttl, $callback);
    }
    
    public function forget(string $key): bool
    {
        if (empty($this->tags)) {
            return Cache::forget($key);
        }
        
        return Cache::tags($this->tags)->forget($key);
    }
    
    public function flush(): bool
    {
        if (empty($this->tags)) {
            return Cache::flush();
        }
        
        return Cache::tags($this->tags)->flush();
    }
    
    public function withTags(array $tags): self
    {
        $instance = clone $this;
        $instance->tags = $tags;
        return $instance;
    }
    
    public function increment(string $key, int $value = 1): int
    {
        return Cache::increment($key, $value);
    }
    
    public function decrement(string $key, int $value = 1): int
    {
        return Cache::decrement($key, $value);
    }
    
    public function lock(string $key, int $seconds = 60): bool
    {
        return Cache::lock($key, $seconds)->get();
    }
    
    public function releaseLock(string $key): void
    {
        Cache::lock($key)->forceRelease();
    }
}