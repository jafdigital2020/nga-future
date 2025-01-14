<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeShiftIdNullableInShiftSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('shift_id')->nullable()->change();
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
            $table->unsignedBigInteger('shift_id')->nullable(false)->change(); // Revert this line if needed
        });
    }
}
