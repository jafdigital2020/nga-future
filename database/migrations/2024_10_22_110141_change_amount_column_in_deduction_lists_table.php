<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAmountColumnInDeductionListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deduction_lists', function (Blueprint $table) {
            $table->decimal('amount', 10, 4)->change();
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
            $table->decimal('amount', 10, 2)->change();
        });
    }
}
