<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
            $table->string('date', 20);
            $table->string('company')->nullable();
            $table->string('month_label')->nullable();
            $table->timestamps();

            $table->unique('date');
            $table->index('import_batch_id');
        });

        Schema::create('log_unit_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
            $table->foreignId('log_day_id')->constrained('log_days')->cascadeOnDelete();
            $table->unsignedTinyInteger('unit');
            $table->string('date', 20);
            $table->string('hour_label', 20);
            $table->unsignedTinyInteger('hour_order')->default(0);
            $table->decimal('rpm', 12, 3)->nullable();
            $table->decimal('v_ry', 12, 3)->nullable();
            $table->decimal('v_yb', 12, 3)->nullable();
            $table->decimal('v_br', 12, 3)->nullable();
            $table->decimal('i_r', 12, 3)->nullable();
            $table->decimal('i_y', 12, 3)->nullable();
            $table->decimal('i_b', 12, 3)->nullable();
            $table->decimal('freq', 12, 3)->nullable();
            $table->decimal('kvar', 12, 3)->nullable();
            $table->decimal('pf', 12, 4)->nullable();
            $table->decimal('kw', 12, 3)->nullable();
            $table->decimal('kwh', 18, 3)->nullable();
            $table->decimal('avr_v', 12, 3)->nullable();
            $table->decimal('avr_i', 12, 3)->nullable();
            $table->timestamps();

            $table->index(['unit', 'date']);
            $table->index(['log_day_id', 'unit', 'hour_order']);
        });

        Schema::create('log_line_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
            $table->foreignId('log_day_id')->constrained('log_days')->cascadeOnDelete();
            $table->string('date', 20);
            $table->string('hour_label', 20);
            $table->unsignedTinyInteger('hour_order')->default(0);
            $table->decimal('v_ry', 12, 3)->nullable();
            $table->decimal('v_yb', 12, 3)->nullable();
            $table->decimal('v_br', 12, 3)->nullable();
            $table->decimal('i_r', 12, 3)->nullable();
            $table->decimal('i_y', 12, 3)->nullable();
            $table->decimal('i_b', 12, 3)->nullable();
            $table->decimal('freq', 12, 3)->nullable();
            $table->decimal('pf', 12, 4)->nullable();
            $table->decimal('kw', 12, 3)->nullable();
            $table->decimal('kvar', 12, 3)->nullable();
            $table->decimal('kwh', 18, 3)->nullable();
            $table->timestamps();

            $table->index(['log_day_id', 'hour_order']);
            $table->index('date');
        });

        Schema::create('log_meter_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
            $table->foreignId('log_day_id')->constrained('log_days')->cascadeOnDelete();
            $table->string('date', 20);
            $table->string('hour_label', 20);
            $table->unsignedTinyInteger('hour_order')->default(0);
            $table->decimal('main_meter', 18, 3)->nullable();
            $table->decimal('check_meter', 18, 3)->nullable();
            $table->decimal('main_diff', 18, 3)->nullable();
            $table->decimal('check_diff', 18, 3)->nullable();
            $table->timestamps();

            $table->index(['log_day_id', 'hour_order']);
            $table->index('date');
        });

        Schema::create('log_unit_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
            $table->foreignId('log_day_id')->constrained('log_days')->cascadeOnDelete();
            $table->unsignedTinyInteger('unit');
            $table->string('date', 20);
            $table->string('total_running', 20)->nullable();
            $table->string('total_outage', 20)->nullable();
            $table->decimal('initial_reading', 18, 3)->nullable();
            $table->decimal('final_reading', 18, 3)->nullable();
            $table->decimal('total_generation_kwh', 18, 3)->nullable();
            $table->decimal('first_kwh', 18, 3)->nullable();
            $table->decimal('last_kwh', 18, 3)->nullable();
            $table->decimal('energy_kwh', 18, 3)->nullable();
            $table->decimal('avg_kw', 12, 3)->nullable();
            $table->unsignedSmallInteger('hour_count')->default(0);
            $table->timestamps();

            $table->unique(['log_day_id', 'unit']);
            $table->index(['unit', 'date']);
        });

        Schema::create('log_unit_outages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
            $table->foreignId('log_day_id')->constrained('log_days')->cascadeOnDelete();
            $table->unsignedTinyInteger('unit');
            $table->string('date', 20);
            $table->unsignedTinyInteger('serial')->nullable();
            $table->string('trip_to', 20)->nullable();
            $table->string('resume_hrs', 20)->nullable();
            $table->string('synch_hrs', 20)->nullable();
            $table->string('outage_hrs', 20)->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['unit', 'date']);
            $table->index('log_day_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_unit_outages');
        Schema::dropIfExists('log_unit_days');
        Schema::dropIfExists('log_meter_hours');
        Schema::dropIfExists('log_line_hours');
        Schema::dropIfExists('log_unit_hours');
        Schema::dropIfExists('log_days');
    }
};
