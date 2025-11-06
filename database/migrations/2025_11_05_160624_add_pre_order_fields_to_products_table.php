<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('pre_order')->default(false)->after('active');
            $table->dateTime('pre_order_date')->nullable()->after('pre_order');
            $table->text('pre_order_message')->nullable()->after('pre_order_date');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['pre_order', 'pre_order_date', 'pre_order_message']);
        });
    }
};
