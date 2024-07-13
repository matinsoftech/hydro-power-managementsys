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
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->integer('station_level');
            $table->unsignedBigInteger('parent_id');
            $table->string('name');
            $table->string('map_name')->nullable();
            $table->integer('capacity');
            $table->string('line_man_name');
            $table->string('manager');
            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};
