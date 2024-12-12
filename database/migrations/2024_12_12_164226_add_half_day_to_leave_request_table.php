<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHalfDayToLeaveRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->boolean('is_half_day')->default(false)->after('type');
            $table->enum('half_day_type', ['first_half', 'second_half'])->nullable()->after('is_half_day');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('is_half_day');
            $table->dropColumn('half_day_type');
        });
    }
}
