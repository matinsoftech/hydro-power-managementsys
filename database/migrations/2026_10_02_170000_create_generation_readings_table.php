<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generation_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')
                ->nullable()
                ->constrained('import_batches')
                ->nullOnDelete();

            $table->string('ad_initial_date', 30)->nullable();
            $table->string('ad_final_date', 30)->nullable();
            $table->string('bs_initial_date', 20); // primary BS day key e.g. 2083-05-01
            $table->string('bs_final_date', 20)->nullable();

            $table->decimal('main_initial', 18, 3)->nullable();
            $table->decimal('main_final', 18, 3)->nullable();
            $table->decimal('main_generation_kwh', 18, 3)->nullable();

            $table->decimal('check_initial', 18, 3)->nullable();
            $table->decimal('check_final', 18, 3)->nullable();
            $table->decimal('check_generation_kwh', 18, 3)->nullable();

            $table->string('month_label')->nullable();
            $table->string('company')->nullable();
            $table->timestamps();

            $table->index('bs_initial_date');
            $table->index(['bs_initial_date', 'bs_final_date']);
            $table->index('import_batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generation_readings');
    }
};
