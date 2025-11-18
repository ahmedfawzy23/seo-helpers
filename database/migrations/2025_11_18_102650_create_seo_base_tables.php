<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->id();
            $table->morphs('seoable'); // seoable_type, seoable_id
            $table->string('page_title')->nullable(); // Page Title (if static page like About Us, Contact Us, etc.)

            // Meta Tags
            $table->string('meta_title', 70)->nullable();
            $table->string('meta_description', 160)->nullable();

            // Open Graph
            $table->string('og_image')->nullable();

            // Additional
            $table->json('extra_meta')->nullable(); // For custom meta tags

            $table->timestamps();

            // Index for performance
            $table->index(['seoable_type', 'seoable_id', 'page_title']);
        });

        Schema::create('scripts', function (Blueprint $table) {
            $table->id();
            $table->morphs('scriptable'); // scriptable_type, scriptable_id
            $table->string('page_title')->nullable();

            $table->text('script')->nullable();
            $table->string('faq')->nullable();
            $table->timestamps();

            // Index for performance
            $table->index(['scriptable_type', 'scriptable_id', 'page_title']);
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_url')->unique();
            $table->string('to_url');
            $table->integer('status_code')->default(301); // 301 or 302
            $table->integer('hits')->default(0); // Track number of hits (which means how many times the redirect was used)
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('from_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_metadata');
        Schema::dropIfExists('scripts');
        Schema::dropIfExists('redirects');
    }
};
