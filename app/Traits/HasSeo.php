<?php
namespace App\Traits;

use App\Models\SeoMetadata;
use App\Models\Script;

trait HasSeo
{
    /**
     * Get the SEO metadata for the model
     */
    public function seo()
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    /**
     * Get the scripts for the model
     */
    public function scripts()
    {
        return $this->morphMany(Script::class, 'scriptable');
    }

    /**
     * Update or create SEO metadata
     */
    public function updateSeo(array $data): SeoMetadata
    {
        return SeoMetadata::updateOrCreateFor($this, $data);
    }

    /**
     * Add or update script
     */
    public function updateScript(array $data): Script
    {
        return $this->scripts()->updateOrCreate(
            ['scriptable_type' => get_class($this), 'scriptable_id' => $this->id],
            $data
        );
    }

    /**
     * Get SEO title
     */
    public function getSeoTitle(): string
    {
        return $this->seo?->getTitle() ?? $this->title ?? config('app.name');
    }

    /**
     * Get SEO description
     */
    public function getSeoDescription(): ?string
    {
        return $this->seo?->meta_description ?? $this->excerpt ?? null;
    }

    /**
     * Get OG image
     */
    public function getOgImage(): ?string
    {
        return $this->seo?->getOgImageUrl() ?? $this->featured_image ?? null;
    }

    /**
     * Check if model has FAQ
     */
    public function hasFaq(): bool
    {
        return $this->scripts()->whereNotNull('faq')->exists();
    }

    /**
     * Get FAQ schema
     */
    public function getFaqSchema(): ?array
    {
        $script = $this->scripts()->whereNotNull('faq')->first();
        return $script?->getFaqSchema();
    }
}
