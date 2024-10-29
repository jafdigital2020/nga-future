<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInclusionColumnsToEarningListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('earning_lists', function (Blueprint $table) {
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
        Schema::table('earning_lists', function (Blueprint $table) {
            $table->dropColumn(['inclusion_limit', 'is_every_payroll']);
        });
    }
}
