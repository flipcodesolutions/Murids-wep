<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('religion_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('time_slot_id')->nullable();
            $table->text('question');
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['religion_id', 'time_slot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
