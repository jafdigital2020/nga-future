<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('month'); 
            $table->string('cut_off'); 
            $table->decimal('monthly_salary', 10, 2); 
            $table->decimal('daily_rate', 10, 2); 
            $table->decimal('hourly_rate', 10, 2); 
            $table->decimal('gross_pay', 10, 2)->default(0);
            // Dynamic Earnings
            $table->json('earnings')->nullable(); // Store dynamic earnings as JSON
            $table->decimal('total_earnings', 10, 2)->default(0); // Total earnings calculated
            
            // Dynamic Deductions
            $table->json('deductions')->nullable(); // Store dynamic deductions as JSON
            $table->decimal('total_deductions', 10, 2)->default(0); // Total deductions calculated
            
            // Loans
            $table->json('loans')->nullable(); // Store loans as JSON
            $table->decimal('total_loans', 10, 2)->default(0); // Total loans calculated
            
            // Final net pay calculation
            $table->decimal('net_pay', 10, 2)->default(0); // Net pay after deductions and loans

            // Timestamps for created_at and updated_at
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
        Schema::dropIfExists('salary_tables');
    }
}
