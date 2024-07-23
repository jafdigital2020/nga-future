<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->string('name')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('days')->nullable();
            $table->text('reason')->nullable();
            $table->enum('type', ['Vacation Leave', 'Sick Leave', 'Birthday Leave', 'Unpaid Leave']);
            $table->enum('status', ['Pending', 'Approved', 'Declined'])->default('Pending');
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
        Schema::dropIfExists('leave_requests');
    }
}
