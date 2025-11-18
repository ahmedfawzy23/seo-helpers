<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMetadata extends Model
{
    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'page_title',
        'meta_title',
        'meta_description',
        'og_image',
        'extra_meta',
    ];

    protected $casts = [
        'extra_meta' => 'array',
    ];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Update or create SEO for a model
     */
    public static function updateOrCreateFor($model, array $data)
    {
        return static::updateOrCreate(
            [
                'seoable_type' => get_class($model),
                'seoable_id' => $model->id,
            ],
            $data
        );
    }

    /**
     * Find by page title (for static pages)
     */
    public static function findByPageTitle(string $pageTitle)
    {
        return static::where('page_title', $pageTitle)
            ->whereNull('seoable_id')
            ->first();
    }

    /**
     * Get meta title or fallback
     */
    public function getTitle(): string
    {
        return $this->meta_title ?? $this->page_title ?? config('app.name');
    }

    /**
     * Get OG image URL
     */
    public function getOgImageUrl(): ?string
    {
        if (!$this->og_image) {
            return null;
        }

        return filter_var($this->og_image, FILTER_VALIDATE_URL)
            ? $this->og_image
            : asset($this->og_image);
    }
}
