<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id'); 
            $table->unsignedBigInteger('deduction_id'); 
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('deduction_id')->references('id')->on('deduction_lists')->onDelete('cascade');
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
        Schema::dropIfExists('user_deductions');
    }
}
