<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHolidayAndSpecialToSalaryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salary_tables', function (Blueprint $table) {
            $table->string('regular_holiday_pay')->nullable()->after('paidLeave');
            $table->string('special_holiday_pay')->nullable()->after('regular_holiday_pay');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salary_tables', function (Blueprint $table) {
            $table->dropColumn('regular_holiday_pay');
            $table->dropColumn('special_holiday_pay');
        });
    }
}
