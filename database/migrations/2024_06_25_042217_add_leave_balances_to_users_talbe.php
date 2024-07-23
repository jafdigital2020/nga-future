<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveBalancesToUsersTalbe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('vacLeave')->nullable();
            $table->integer('sickLeave')->nullable();
            $table->integer('bdayLeave')->nullable();
            $table->integer('otherLeave')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['vacLeave', 'sickLeave', 'bdayLeave', 'otherLeave']);
        });
    }
}
