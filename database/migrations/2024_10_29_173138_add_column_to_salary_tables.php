<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToSalaryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salary_tables', function (Blueprint $table) {
            $table->string('basic_pay')->nullable()->after('paidLeave');
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
           $table->dropColumn('basic_pay');
        });
    }
}
