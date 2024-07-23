<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovedAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approved_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->string('name')->nullable();
            $table->string('department')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('month')->nullable();
            $table->string('cut_off')->nullable();
            $table->string('totalHours')->nullable();
            $table->string('totalLate')->nullable();
            $table->string('otHours')->nullable();
            $table->string('vacLeave')->nullable();
            $table->string('sickLeave')->nullable();
            $table->string('bdayLeave')->nullable();
            $table->string('unpaidLeave')->nullable();
            $table->string('status')->default('pending');
            $table->string('payroll_by')->nullable();
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
        Schema::dropIfExists('approved_attendances');
    }
}
