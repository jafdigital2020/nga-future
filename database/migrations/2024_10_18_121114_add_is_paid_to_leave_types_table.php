<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPaidToLeaveTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->boolean('is_paid')->default(1); // 1 means paid, 0 means unpaid
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn('is_paid');
        });
    }
}
