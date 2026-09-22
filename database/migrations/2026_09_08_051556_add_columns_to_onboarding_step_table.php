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
            if (Schema::hasColumn('onboarding_step', 'options')) {
                $table->dropColumn('options');
            }
            if (!Schema::hasColumn('onboarding_step', 'yes_response')) {
                $table->string('yes_response')->nullable();
            }
            if (!Schema::hasColumn('onboarding_step', 'no_response')) {
                $table->string('no_response')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onboarding_step', function (Blueprint $table) {
            if (Schema::hasColumn('onboarding_step', 'yes_response')) {
                $table->dropColumn('yes_response');
            }
            if (Schema::hasColumn('onboarding_step', 'no_response')) {
                $table->dropColumn('no_response');
            }
        });
    }
};
