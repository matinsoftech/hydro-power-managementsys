<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_batches', function (Blueprint $table) {
            $table->unsignedInteger('skipped_count')->default(0)->after('record_count');
            $table->unsignedInteger('duplicate_count')->default(0)->after('skipped_count');
        });

        // Remove existing duplicate failure rows (keep earliest id per unique event)
        $rows = DB::table('grid_failures')
            ->orderBy('id')
            ->get(['id', 'unit', 'date', 'from_hrs', 'to_hrs', 'synch_hrs', 'duration_hrs', 'reason']);

        $seen = [];
        $deleteIds = [];

        foreach ($rows as $row) {
            $key = implode('|', [
                (int) $row->unit,
                (string) $row->date,
                (string) ($row->from_hrs ?? ''),
                (string) ($row->to_hrs ?? ''),
                (string) ($row->synch_hrs ?? ''),
                (string) ($row->duration_hrs ?? ''),
                mb_strtolower(trim((string) ($row->reason ?? ''))),
            ]);

            if (isset($seen[$key])) {
                $deleteIds[] = $row->id;
            } else {
                $seen[$key] = true;
            }
        }

        foreach (array_chunk($deleteIds, 500) as $chunk) {
            DB::table('grid_failures')->whereIn('id', $chunk)->delete();
        }
    }

    public function down(): void
    {
        Schema::table('import_batches', function (Blueprint $table) {
            $table->dropColumn(['skipped_count', 'duplicate_count']);
        });
    }
};
