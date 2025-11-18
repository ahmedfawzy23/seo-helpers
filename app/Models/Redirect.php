<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = [
        'from_url',
        'to_url',
        'status_code',
        'hits',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'hits' => 'integer',
        'status_code' => 'integer',
    ];

    /**
     * Increment hits counter
     */
    public function incrementHits(): void
    {
        $this->increment('hits');
    }

    /**
     * Find redirect by URL
     */
    public static function findByUrl(string $url): ?self
    {
        return static::where('from_url', $url)
            ->where('active', true)
            ->first();
    }

    /**
     * Scope for active redirects
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get normalized from URL
     */
    public function getNormalizedFromUrl(): string
    {
        return trim($this->from_url, '/');
    }

    /**
     * Get normalized to URL
     */
    public function getNormalizedToUrl(): string
    {
        return trim($this->to_url, '/');
    }
}
