<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOffsetDateToOvertimeRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            $table->string('offset_date')->nullable()->after('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('overtime_requests', function (Blueprint $table) {
            $table->dropColumn('offset_date');
        });
    }
}
