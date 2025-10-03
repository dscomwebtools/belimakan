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
        Schema::table('delivery_states', function (Blueprint $table) {
            $table->string('biteship_status')
                ->nullable()
                ->unique()
                ->after('name');

            $table->string('biteship_status_name')
                ->nullable()
                ->after('biteship_status');

            $table->boolean('is_biteship_status')
                ->default(false)
                ->after('biteship_status_name');

            $table->string('description')
                ->nullable()
                ->after('is_biteship_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_states', function (Blueprint $table) {
            $table->dropColumn(['biteship_status', 'biteship_status_name', 'is_biteship_status']);
        });
    }
};
