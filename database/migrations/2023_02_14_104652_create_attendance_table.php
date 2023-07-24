<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('timeIn');
            $table->string('breakIn')->nullable();
            $table->string('breakEnd')->nullable();
            $table->string('breakOut')->nullable();
            $table->string('timeOut')->nullable();
            $table->string('timeEnd')->nullable();
            $table->string('timeTotal')->nullable();
            $table->string('status')->nullable();
            $table->string('totalLate')->nullable();
            $table->string('breakLate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance');
    }
}
