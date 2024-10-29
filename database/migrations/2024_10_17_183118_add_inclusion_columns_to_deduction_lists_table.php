<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInclusionColumnsToDeductionListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deduction_lists', function (Blueprint $table) {
            $table->integer('inclusion_limit')->nullable();
            $table->boolean('is_every_payroll')->default(0); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deduction_lists', function (Blueprint $table) {
            $table->dropColumn(['inclusion_limit', 'is_every_payroll']);
        });
    }
}
