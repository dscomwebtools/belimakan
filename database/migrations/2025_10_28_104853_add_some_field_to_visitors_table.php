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
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->after('count');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->boolean('is_bot')->default(false)->after('user_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'user_agent', 'is_bot']);
        });
    }
};
