<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id'); 
            $table->unsignedBigInteger('earning_id'); 
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('earning_id')->references('id')->on('earning_lists')->onDelete('cascade');
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
        Schema::dropIfExists('user_earnings');
    }
}
