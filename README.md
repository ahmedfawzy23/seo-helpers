# SEO Helpers for Laravel

Composable SEO utilities for Laravel applications. Store and retrieve page metadata, Open Graph images, JSON-LD FAQ scripts, and HTTP redirects through a small set of Eloquent models and traits.

This project focuses on giving any Laravel model (or static page) a consistent SEO surface while keeping the schema minimal and browser-rendering agnostic.

## Features

-   Polymorphic SEO metadata per model or static page via `HasSeo` and `HasStaticSeo` traits.
-   Meta title, description, Open Graph image, and extensible `extra_meta` storage with sensible fallbacks to model fields or the app name.
-   Script/FAQ management with JSON and JSON-LD generation helpers.
-   Redirect registry with active filtering, hit counting, and normalization helpers.
-   Single migration that provisions `seo_metadata`, `scripts`, and `redirects` tables.

## Architecture & Data Model

-   `seo_metadata` stores meta title/description, OG image, and custom meta for any `seoable` morph target or a static `page_title` (`database/migrations/2025_11_18_102650_create_seo_base_tables.php`).
-   `scripts` stores arbitrary scripts or FAQ JSON for any `scriptable` morph target or static `page_title` (same migration).
-   `redirects` tracks `from_url` → `to_url` mappings with status code, hit counter, and active flag.
-   Relationships live in traits so models stay lean:
    -   `HasSeo` adds `seo()` and `scripts()` morph relations plus convenience getters/fallbacks (`app/Traits/HasSeo.php`).
    -   `HasStaticSeo` enables the same API for static pages that have no database model (`app/Traits/HasStaticSeo.php`).

## Project Structure

```
app/
  Models/
    Redirect.php          # Redirect registry with helpers
    Script.php            # Scripts + FAQ JSON/JSON-LD utilities
    SeoMetadata.php       # Meta tags, OG image, extra meta
  Traits/
    HasSeo.php            # Attach SEO + scripts to Eloquent models
    HasStaticSeo.php      # Static page SEO/scripts helpers
database/
  migrations/2025_11_18_102650_create_seo_base_tables.php  # SEO + scripts + redirects schema
```

## Getting Started

Requirements: PHP 8.2 or higher, Composer, and a database supported by Laravel.

```bash
git clone <repo-url>
cd seo
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Run the app locally:

```bash
php artisan serve
```

## Usage Examples

### Attach SEO to an Eloquent model

```php
use App\Models\Post;
use App\Traits\HasSeo;

class Post extends Model
{
    use HasSeo;
}

$post = Post::find(1);

// Create or update SEO metadata
$post->updateSeo([
    'meta_title' => 'My Post',
    'meta_description' => 'Summary of my post.',
    'og_image' => 'https://cdn.example.com/og/post-1.jpg',
    'extra_meta' => ['robots' => 'index,follow'],
]);

// Attach a script or FAQ (JSON or array)
$post->updateScript([
    'script' => '<script>console.log("page view")</script>',
    'faq' => json_encode([
        ['question' => 'What is this?', 'answer' => 'An example.'],
    ]),
]);

$title = $post->getSeoTitle();          // meta_title → post title → app.name
$description = $post->getSeoDescription(); // meta_description → post excerpt
$ogImage = $post->getOgImage();         // OG URL with asset() fallback handling
$hasFaq = $post->hasFaq();              // true if any attached script has FAQ
$faqSchema = $post->getFaqSchema();     // JSON-LD array or null
```

### Static page SEO (no model needed)

```php
use App\Traits\HasStaticSeo;

class StaticPage
{
    use HasStaticSeo;
}

// Upsert SEO for a static page title
StaticPage::updateSeoForPage('About Us', [
    'meta_title' => 'About Us - Company',
    'meta_description' => 'Learn about our team and mission.',
]);

StaticPage::updateScriptForPage('About Us', [
    'faq' => [
        ['question' => 'Where are you located?', 'answer' => 'Remote-first.'],
    ],
]);

$seo = StaticPage::getSeo('About Us');       // SeoMetadata or null
$script = StaticPage::getScript('About Us'); // Script or null
```

### Working directly with `SeoMetadata`

```php
use App\Models\SeoMetadata;

SeoMetadata::updateOrCreateFor($post, [
    'meta_title' => 'Custom Title',
]);

$staticSeo = SeoMetadata::findByPageTitle('Contact');
$title = $staticSeo?->getTitle();      // meta_title → page_title → app.name
$ogUrl = $staticSeo?->getOgImageUrl(); // Absolute URL or asset() generated URL
```

### Scripts and FAQ helpers

```php
use App\Models\Script;

$script = Script::findByPageTitle('Pricing');
$faqs = $script?->getFaqArray();          // Parsed FAQ array
$schema = $script?->getFaqSchema();       // JSON-LD array structure
$schemaJson = $script?->getFaqSchemaJson(); // Pretty JSON string
```

### Redirect registry

```php
use App\Models\Redirect;

Redirect::create([
    'from_url' => '/old-page',
    'to_url' => '/new-page',
    'status_code' => 301,
]);

if ($redirect = Redirect::findByUrl('/old-page')) {
    $redirect->incrementHits();               // Track usage
    $from = $redirect->getNormalizedFromUrl(); // "old-page"
    $to = $redirect->getNormalizedToUrl();     // "new-page"
}

$activeRedirects = Redirect::active()->get(); // Only active redirects
```

## Contribution

1. Fork and create a feature branch.
2. Make changes and commit.
3. Open a pull request that describes the change and any schema updates.

## License

Licensed under the [MIT License](https://opensource.org/licenses/MIT).

## Maintainer

## Author

[Ahmed Fawzy](https://github.com/ahmedfawzy23)

Made with ❤ in [Digital Bonds](https://digitalbondmena.com), Egypt.
