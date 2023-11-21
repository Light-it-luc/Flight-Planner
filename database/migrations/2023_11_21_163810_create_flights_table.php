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
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number', 255)->unique();
            $table->foreignId('airline_id')->constrained()->onDelete('cascade');
            $table->foreignId('origin_city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreignId('dest_city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->dateTimeTz('departure');
            $table->dateTimeTz('arrival');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
