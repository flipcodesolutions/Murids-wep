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
        Schema::table('onboarding_step', function (Blueprint $table) {
            if (!Schema::hasColumn('onboarding_step', 'yes_response_title')) {
                $table->string('yes_response_title')->nullable();
            }
            if (!Schema::hasColumn('onboarding_step', 'no_response_title')) {
                $table->string('no_response_title')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboarding_step', function (Blueprint $table) {
            //
        });
    }
};
