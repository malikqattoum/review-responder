<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('external_id')->nullable();
            $table->enum('source', ['google', 'yelp', 'manual'])->default('manual');
            $table->string('author_name');
            $table->unsignedTinyInteger('rating');
            $table->text('text')->nullable();
            $table->enum('sentiment', ['positive', 'neutral', 'negative'])->nullable();
            $table->date('review_date')->nullable();
            $table->boolean('is_responded')->default(false);
            $table->timestamps();

            $table->index(['business_id', 'sentiment']);
            $table->index(['business_id', 'source']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
