<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOutCapturedImageToClockOut extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->decimal('clock_out_latitude', 10, 7)->nullable()->after('image_path');
            $table->decimal('clock_out_longitude', 10, 7)->nullable()->after('clock_out_latitude');
            $table->string('clock_out_image_path')->nullable()->after('clock_out_longitude');
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
            $table->dropColumn('clock_out_latitude');
            $table->dropColumn('clock_out_longitude');
            $table->dropColumn('clock_out_image_path');
        });
    }
}
