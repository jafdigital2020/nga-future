<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHolidaysToApprovedAttendance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approved_attendances', function (Blueprint $table) {
            $table->string('regular_holiday_hours')->nullable()->after('approvedOvertime');
            $table->string('special_holiday_hours')->nullable()->after('regular_holiday_hours');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approved_attendances', function (Blueprint $table) {
            $table->dropColumn('regular_holiday_hours');
            $table->dropColumn('special_holiday_hours');
        });
    }
}
