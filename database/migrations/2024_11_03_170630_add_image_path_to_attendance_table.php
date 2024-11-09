<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImagePathToAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->string('image_path')->nullable();
            $table->string('status_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropColumn('image_path');
            $table->dropColumn('status_code');
        });
    }
}
