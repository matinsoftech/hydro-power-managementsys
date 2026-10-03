<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grid_failures', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('unit'); // 1 or 2
            $table->string('date', 20); // Nepali BS date e.g. 2083-05-02
            $table->string('from_hrs', 20)->nullable();
            $table->string('to_hrs', 20)->nullable();
            $table->string('synch_hrs', 20)->nullable();
            $table->string('duration_hrs', 20)->nullable();
            $table->text('reason')->nullable();
            $table->string('month_label')->nullable();
            $table->string('company')->nullable();
            $table->timestamps();

            $table->index(['unit', 'date']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grid_failures');
    }
};
