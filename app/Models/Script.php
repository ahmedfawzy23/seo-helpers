<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Script extends Model
{
    protected $fillable = [
        'scriptable_type',
        'scriptable_id',
        'page_title',
        'script',
        'faq',
    ];

    public function scriptable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Find by page title (for static pages)
     */
    public static function findByPageTitle(string $pageTitle)
    {
        return static::where('page_title', $pageTitle)
            ->whereNull('scriptable_id')
            ->first();
    }

    /**
     * Parse FAQ JSON string to array
     */
    public function getFaqArray(): ?array
    {
        if (!$this->faq) {
            return null;
        }

        return is_string($this->faq)
            ? json_decode($this->faq, true)
            : $this->faq;
    }

    /**
     * Generate FAQ Schema JSON-LD
     */
    public function getFaqSchema(): ?array
    {
        $faqs = $this->getFaqArray();

        if (!$faqs) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => collect($faqs)->map(fn($faq) => [
                '@type' => 'Question',
                'name' => $faq['question'] ?? '',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'] ?? ''
                ]
            ])->toArray()
        ];
    }

    /**
     * Get FAQ Schema as JSON string
     */
    public function getFaqSchemaJson(): ?string
    {
        $schema = $this->getFaqSchema();

        return $schema
            ? json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            : null;
    }
}
