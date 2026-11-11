<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exclusionary_terms', function (Blueprint $table) {
            $table->id();
            $table->string('phrase');
            $table->string('category')->nullable();
            $table->string('suggestion')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exclusionary_terms');
    }
};