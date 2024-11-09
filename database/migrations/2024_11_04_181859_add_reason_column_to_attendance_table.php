<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReasonColumnToAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
          $table->string('reason')->nullable();
          $table->unsignedBigInteger('approved_by')->nullable()->after('reason');
          $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['approved_by']); 
            $table->dropColumn('approved_by', 'reason');  
        });
    }
}
