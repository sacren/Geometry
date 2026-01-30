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
        Schema::table('posts', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->json('meta_data')->nullable();

            // Add indexes for faster queries
            $table->index('published_at');
            $table->index('is_published');
            // Note: JSON columns typically don't need a simple index like this
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['published_at']);
            $table->dropIndex(['is_published']);
            // Note: We are not dropping an index for meta_data since JSON columns
            // typically don't have a simple index that can be dropped
            $table->dropColumn(['published_at', 'is_published', 'meta_data']);
        });
    }
};
