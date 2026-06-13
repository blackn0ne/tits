<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['project_category_id', 'language_id'], 'proj_cat_trans_cat_lang_uniq');
            $table->unique(['language_id', 'slug'], 'proj_cat_trans_lang_slug_uniq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_category_translations');
    }
};
