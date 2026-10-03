<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50); // failure | generation
            $table->string('original_filename');
            $table->string('stored_path')->nullable();
            $table->unsignedInteger('record_count')->default(0);
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('imported_at');
            $table->timestamp('undone_at')->nullable();
            $table->foreignId('undone_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'imported_at']);
        });

        Schema::table('grid_failures', function (Blueprint $table) {
            $table->foreignId('import_batch_id')
                ->nullable()
                ->after('id')
                ->constrained('import_batches')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('grid_failures', function (Blueprint $table) {
            $table->dropConstrainedForeignId('import_batch_id');
        });

        Schema::dropIfExists('import_batches');
    }
};
