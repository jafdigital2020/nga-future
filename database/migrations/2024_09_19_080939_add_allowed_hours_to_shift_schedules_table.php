<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowedHoursToShiftSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_schedules', function (Blueprint $table) {
            $table->string('allowedHours')->nullable()->after('isFlexibleTime');
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
            $table->dropColumn('allowedHours');
        });
    }
}
