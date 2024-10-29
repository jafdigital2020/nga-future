<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaveTypeIdToLeaveRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('leave_type_id')->after('users_id'); 
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['leave_type_id']);
            $table->dropColumn('leave_type_id'); 
        });
    }
}
