<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToSalaryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salary_tables', function (Blueprint $table) {
            $table->string('status')->nullable()->after('net_pay');
            $table->unsignedBigInteger('approved_attendance_id')->after('users_id');
            $table->foreign('approved_attendance_id')->references('id')->on('approved_attendances')->onDelete('cascade');
            $table->string('year')->nullable()->after('month');
            $table->string('start_date')->nullable()->after('cut_off');
            $table->string('end_date')->nullable()->after('start_date');
            $table->string('total_hours')->nullable()->after('monthly_salary');
            $table->string('notes')->nullable()->after('net_pay');
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
            $table->dropColumn('status');
            $table->dropForeign(['approved_attendance_id']);
            $table->dropColumn('approved_attendance_id');
            $table->dropColumn('year');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('total_hours');
            $table->dropColumn('notes');
        });
    }
}
