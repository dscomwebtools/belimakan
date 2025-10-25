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
        Schema::table('sosmeds', function (Blueprint $table) {
            $table->boolean('is_footer')->default(false)->after('url');
            $table->boolean('is_link')->default(false)->after('is_footer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sosmeds', function (Blueprint $table) {
            $table->dropColumn(['is_footer', 'is_link']);
        });
    }
};
