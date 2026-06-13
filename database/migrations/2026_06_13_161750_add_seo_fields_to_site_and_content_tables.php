<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('title_separator', 10)->default(' - ')->after('maintenance_mode');
            $table->string('default_robots', 50)->default('index, follow')->after('title_separator');
            $table->string('og_image_path')->nullable()->after('favicon_path');
            $table->string('twitter_handle')->nullable()->after('og_image_path');
            $table->string('google_site_verification')->nullable()->after('twitter_handle');
            $table->string('yandex_verification')->nullable()->after('google_site_verification');
            $table->boolean('sitemap_enabled')->default(true)->after('yandex_verification');
            $table->text('robots_txt')->nullable()->after('sitemap_enabled');
        });

        Schema::table('site_setting_translations', function (Blueprint $table) {
            $table->string('home_title')->nullable()->after('keywords');
            $table->text('home_meta_description')->nullable()->after('home_title');
            $table->string('blog_index_title')->nullable()->after('home_meta_description');
            $table->text('blog_index_meta_description')->nullable()->after('blog_index_title');
            $table->string('projects_index_title')->nullable()->after('blog_index_meta_description');
            $table->text('projects_index_meta_description')->nullable()->after('projects_index_title');
        });

        Schema::table('blog_post_translations', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('title');
            $table->text('meta_description')->nullable()->after('meta_title');
        });

        Schema::table('project_translations', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('title');
            $table->text('meta_description')->nullable()->after('meta_title');
        });
    }

    public function down(): void
    {
        Schema::table('project_translations', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description']);
        });

        Schema::table('blog_post_translations', function (Blueprint $table) {
            $table->dropColumn(['meta_title', 'meta_description']);
        });

        Schema::table('site_setting_translations', function (Blueprint $table) {
            $table->dropColumn([
                'home_title',
                'home_meta_description',
                'blog_index_title',
                'blog_index_meta_description',
                'projects_index_title',
                'projects_index_meta_description',
            ]);
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'title_separator',
                'default_robots',
                'og_image_path',
                'twitter_handle',
                'google_site_verification',
                'yandex_verification',
                'sitemap_enabled',
                'robots_txt',
            ]);
        });
    }
};
