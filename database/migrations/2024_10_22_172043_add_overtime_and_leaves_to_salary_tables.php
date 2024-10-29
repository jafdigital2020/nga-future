<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOvertimeAndLeavesToSalaryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salary_tables', function (Blueprint $table) {
           $table->string('overtimeHours')->nullable()->after('hourly_rate');
           $table->string('paidLeave')->nullable()->after('overtimeHours');
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
           $table->dropColumn('overtimeHours');
           $table->dropColumn('paidLeave');
        });
    }
}
