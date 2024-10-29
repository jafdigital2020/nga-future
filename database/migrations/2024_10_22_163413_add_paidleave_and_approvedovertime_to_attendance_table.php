<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidleaveAndApprovedovertimeToAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approved_attendances', function (Blueprint $table) {
            $table->string('paidLeave')->default(0)->after('unpaidLeave')->nullable();
            $table->string('approvedOvertime')->default(0)->after('totalLate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approved_attendances', function (Blueprint $table) {
            $table->dropColumn('paidLeave');
            $table->dropColumn('approvedOvertime');
        });
    }
}
