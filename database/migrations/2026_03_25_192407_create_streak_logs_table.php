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
        Schema::create('streak_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streak_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['done', 'skipped']);
            $table->timestamps();

            $table->unique(['streak_id', 'date']);
            $table->index(['streak_id', 'status']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streak_logs');
    }
};
