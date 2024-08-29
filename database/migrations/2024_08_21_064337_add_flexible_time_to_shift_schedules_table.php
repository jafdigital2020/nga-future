<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlexibleTimeToShiftSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_schedules', function (Blueprint $table) {
            $table->boolean('isFlexibleTime')->default(false)->after('shiftEnd');
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
            $table->dropColumn('isFlexibleTime');
        });
    }
}
