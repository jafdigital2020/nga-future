<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditAndCreateColumnsToGeofencingSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('geofencing_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('fencing_radius');
            $table->unsignedBigInteger('edit_by')->nullable()->after('created_by');

            // Add foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('edit_by')->references('id')->on('users')->onDelete('set null');
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
            $table->dropForeign(['created_by']);
            $table->dropForeign(['edit_by']);
            $table->dropColumn(['created_by', 'edit_by']);
        });
    }
}
