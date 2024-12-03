<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHolidayUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('holiday_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('holiday_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('holiday_id')->references('id')->on('settings_holidays')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('holiday_user');
    }
}
