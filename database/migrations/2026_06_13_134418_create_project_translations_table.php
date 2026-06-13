<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->longText('content')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'language_id'], 'proj_trans_proj_lang_uniq');
            $table->unique(['language_id', 'slug'], 'proj_trans_lang_slug_uniq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_translations');
    }
};
