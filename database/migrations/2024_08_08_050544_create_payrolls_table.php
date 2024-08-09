<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('ename')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->string('cut_off')->nullable();
            $table->string('year')->nullable();
            $table->string('transactionDate')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('month')->nullable();
            $table->string('totalHours')->nullable();
            $table->string('totalLate')->nullable();
            $table->string('sss')->nullable();
            $table->string('philHealth')->nullable();
            $table->string('pagIbig')->nullable();
            $table->string('withHolding')->nullable();
            $table->string('late')->nullable();
            $table->string('loan')->nullable();
            $table->string('advance')->nullable();
            $table->string('others')->nullable();
            $table->string('bdayLeave')->nullable();
            $table->string('vacLeave')->nullable();
            $table->string('sickLeave')->nullable();
            $table->string('regHoliday')->nullable();
            $table->string('otTotal')->nullable();
            $table->string('nightDiff')->nullable();
            $table->string('bonus')->nullable();
            $table->string('totalDeduction')->nullable();
            $table->string('totalEarning')->nullable();
            $table->string('grossMonthly')->nullable();
            $table->string('grossBasic')->nullable();
            $table->string('dailyRate')->nullable();
            $table->string('hourlyRate')->nullable();
            $table->string('netPayTotal')->nullable();
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
        Schema::dropIfExists('payrolls');
    }
}
