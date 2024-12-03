<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDeviceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_device', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User reference
            $table->foreignId('biometric_device_id')->constrained()->onDelete('cascade'); // Device reference
            $table->integer('biometric_user_id')->nullable(); // Biometric ID specific to this device
            $table->timestamps(); // Created_at and updated_at
        
            $table->unique(['user_id', 'biometric_device_id']); // Ensure one user-device pair
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_device');
    }
}
