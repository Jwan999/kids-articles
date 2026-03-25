<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('articles', 'issdarat');
        Schema::rename('article_category', 'issdar_category');

        Schema::table('issdar_category', function (Blueprint $table) {
            $table->renameColumn('article_id', 'issdar_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->renameColumn('article_id', 'issdar_id');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->renameColumn('issdar_id', 'article_id');
        });

        Schema::table('issdar_category', function (Blueprint $table) {
            $table->renameColumn('issdar_id', 'article_id');
        });

        Schema::rename('issdar_category', 'article_category');
        Schema::rename('issdarat', 'articles');
    }
};