<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveVacSickBdayLeavesFromYourTableName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approved_attendances', function (Blueprint $table) {
            $table->dropColumn(['vacLeave', 'sickLeave', 'bdayLeave']);
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
            //
        });
    }
}
