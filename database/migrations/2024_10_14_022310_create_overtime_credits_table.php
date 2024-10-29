<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOvertimeCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overtime_credits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id')->nullable();
            $table->foreign('users_id')->references('id')->on('users')->onDelete('set null');
            $table->time('otCredits')->default('16:00:00');
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
        Schema::dropIfExists('overtime_credits');
    }
}
