<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id')->nullable();
            $table->foreign('users_id')->references('id')->on('users')->onDelete('set null');
            $table->string('employee_name')->nullable();
            $table->string('total_hours')->nullable();
            $table->string('total_late')->nullable();
            $table->string('payroll_date')->nullable();
            $table->string('payroll_start')->nullable();
            $table->string('payroll_end')->nullable();
            $table->string('position')->nullable();
            $table->string('regular_holiday')->nullable();
            $table->string('special_holiday')->nullable();
            $table->string('working_on_restday')->nullable();
            $table->string('working_on_weekend')->nullable();
            $table->string('working_on_nightshift')->nullable();
            $table->string('birthday_pto_leave')->nullable();
            $table->string('late')->nullable();
            $table->string('absence')->nullable();
            $table->string('withholding_tax')->nullable();
            $table->string('sss')->nullable();
            $table->string('pag_ibig')->nullable();
            $table->string('phil_health')->nullable();
            $table->string('overtime')->nullable();
            $table->string('thirteenth_month')->nullable();
            $table->string('christmas_bonus')->nullable();
            $table->string('food_allowance')->nullable();
            $table->string('performance_bonus')->nullable();
            $table->string('others')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('late_deduction')->nullable();
            $table->string('earnings')->nullable();
            $table->string('total_deduct')->nullable();
            $table->string('gross_monthly')->nullable();
            $table->string('gross_basic')->nullable();
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
        Schema::dropIfExists('employee_salaries');
    }
}
