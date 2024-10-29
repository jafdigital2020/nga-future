<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id'); 
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('loan_name');
            $table->decimal('amount', 10, 2);
            $table->integer('payable_in_cutoff');
            $table->decimal('payable_amount_per_cutoff', 10, 2);
            $table->string('status');
            $table->date('date_completed')->nullable();
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
        Schema::dropIfExists('loans');
    }
}
