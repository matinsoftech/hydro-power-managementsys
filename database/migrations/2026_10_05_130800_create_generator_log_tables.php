<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generator_daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')
                ->nullable()
                ->constrained('import_batches')
                ->nullOnDelete();
            $table->unsignedTinyInteger('generator');
            $table->string('date', 20);
            $table->string('total_running', 20)->nullable();
            $table->string('total_outage', 20)->nullable();
            $table->decimal('initial_reading', 18, 3)->nullable();
            $table->decimal('final_reading', 18, 3)->nullable();
            $table->decimal('total_generation_kwh', 18, 3)->nullable();
            $table->string('company')->nullable();
            $table->string('month_label')->nullable();
            $table->timestamps();

            $table->index(['generator', 'date']);
            $table->index('date');
            $table->index('import_batch_id');
        });

        Schema::create('generator_outages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')
                ->nullable()
                ->constrained('import_batches')
                ->nullOnDelete();
            $table->foreignId('generator_daily_log_id')
                ->nullable()
                ->constrained('generator_daily_logs')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('generator');
            $table->string('date', 20);
            $table->unsignedTinyInteger('serial')->nullable();
            $table->string('trip_to', 20)->nullable();
            $table->string('resume_hrs', 20)->nullable();
            $table->string('synch_hrs', 20)->nullable();
            $table->string('outage_hrs', 20)->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['generator', 'date']);
            $table->index('date');
            $table->index('import_batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generator_outages');
        Schema::dropIfExists('generator_daily_logs');
    }
};
