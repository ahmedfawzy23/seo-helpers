<?php

namespace App\Traits;

use App\Models\SeoMetadata;
use App\Models\Script;

trait HasStaticSeo
{
    /**
     * Get SEO metadata for a static page
     */
    public static function getSeo(string $pageTitle): ?SeoMetadata
    {
        return SeoMetadata::findByPageTitle($pageTitle);
    }

    /**
     * Get script for a static page
     */
    public static function getScript(string $pageTitle): ?Script
    {
        return Script::findByPageTitle($pageTitle);
    }

    /**
     * Create or update SEO for static page
     */
    public static function updateSeoForPage(string $pageTitle, array $data): SeoMetadata
    {
        return SeoMetadata::updateOrCreate(
            [
                'page_title' => $pageTitle,
                'seoable_type' => null,
                'seoable_id' => null,
            ],
            array_merge($data, ['page_title' => $pageTitle])
        );
    }

    /**
     * Create or update script for static page
     */
    public static function updateScriptForPage(string $pageTitle, array $data): Script
    {
        return Script::updateOrCreate(
            [
                'page_title' => $pageTitle,
                'scriptable_type' => null,
                'scriptable_id' => null,
            ],
            array_merge($data, ['page_title' => $pageTitle])
        );
    }
}
