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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')
                ->nullable()
                ->after('is_paid')
                ->constrained()
                ->nullOnDelete();

            $table->string('coupon_code')
                ->nullable()
                ->after('coupon_id');

            $table->unsignedBigInteger('coupon_discount')
                ->default(0)
                ->after('coupon_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);   // drop the FK constraint
            $table->dropColumn('coupon_id');
            $table->dropColumn('coupon_discount');
        });
    }
};
