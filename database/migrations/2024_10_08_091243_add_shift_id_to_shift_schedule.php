<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShiftIdToShiftSchedule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_schedules', function (Blueprint $table) {
            // Check if the shift_id column exists before adding it
            if (!Schema::hasColumn('shift_schedules', 'shift_id')) {
                $table->foreignId('shift_id')->nullable()->constrained()->onDelete('cascade');
            }

            // Check if the date column exists before adding it
            if (!Schema::hasColumn('shift_schedules', 'date')) {
                $table->date('date')->nullable();  // Add nullable initially, then update existing data if needed
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_schedules', function (Blueprint $table) {
            // Drop shift_id and date columns if they exist
            if (Schema::hasColumn('shift_schedules', 'shift_id')) {
                $table->dropForeign(['shift_id']);
                $table->dropColumn('shift_id');
            }

            if (Schema::hasColumn('shift_schedules', 'date')) {
                $table->dropColumn('date');
            }
        });
    }
}
