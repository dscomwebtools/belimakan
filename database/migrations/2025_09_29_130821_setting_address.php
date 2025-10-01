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
        Schema::create('setting_address', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('hp', 100);
            $table->string('email', 100);
            $table->text('address');
            $table->string('area_id');
            $table->string('area_name');
            $table->string('postal_code');
            $table->string('province');
            $table->string('city');
            $table->string('district');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_address');
    }
};
