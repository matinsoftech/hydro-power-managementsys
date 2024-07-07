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
        Schema::create('grids', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('gone_time');
            $table->time('charge_time')->nullable();
            $table->time('unit_one_sync_time')->nullable();
            $table->time('unit_two_sync_time')->nullable();
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
        Schema::dropIfExists('grids');
    }
};
