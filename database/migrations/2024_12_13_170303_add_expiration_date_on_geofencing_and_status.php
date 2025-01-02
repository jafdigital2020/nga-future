<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpirationDateOnGeofencingAndStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('geofencing_settings', function (Blueprint $table) {
            $table->string('expiration_date')->nullable();
            $table->enum('status', ['Active', 'Expired', 'Never Expired'])->default('Active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('geofencing_settings', function (Blueprint $table) {
            $table->dropColumn('expiration_date');
            $table->dropColumn('status');
        });
    }
}
