<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id'); 
            $table->unsignedBigInteger('asset_id'); 
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->string('assign_date')->nullable();
            $table->string('return_date')->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('user_assets');
    }
}
