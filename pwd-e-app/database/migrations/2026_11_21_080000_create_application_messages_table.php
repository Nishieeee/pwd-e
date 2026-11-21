<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->string('type')->default('message'); // message, accommodation_inquiry, system
            $table->json('metadata')->nullable(); // topic, resolved, etc.
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['application_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_messages');
    }
};
