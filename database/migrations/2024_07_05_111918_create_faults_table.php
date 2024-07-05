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
        Schema::create('faults', function (Blueprint $table) {
            $table->id();
            $table->time('fault_time');
            $table->string('reason');
            $table->longText('photo')->nullable();
            $table->longText('video')->nullable();
            $table->enum('status',['Solved','Unsolved'])->default('Unsolved');
            $table->unsignedBigInteger('solved_by');
            $table->timestamps();

            $table->foreign('solved_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faults');
    }
};
