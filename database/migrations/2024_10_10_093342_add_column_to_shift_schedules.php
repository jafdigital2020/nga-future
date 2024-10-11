<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToShiftSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_schedules', function (Blueprint $table) {
            $table->boolean('recurring')->default(false)->nullable();
            $table->json('selected_days')->nullable(); // 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_schedules', function (Blueprint $table) {
            $table->dropColumn('recurring');
            $table->dropColumn('selected_days');
        });
    }
}
