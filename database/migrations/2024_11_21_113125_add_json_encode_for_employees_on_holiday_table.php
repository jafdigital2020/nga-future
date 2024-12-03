<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJsonEncodeForEmployeesOnHolidayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_holidays', function (Blueprint $table) {
            $table->boolean('recurring')->default(false)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings_holidays', function (Blueprint $table) {
            $table->dropColumn('recurring');
        });
    }
}
