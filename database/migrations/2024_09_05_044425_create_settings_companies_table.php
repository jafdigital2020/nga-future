<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings_companies', function (Blueprint $table) {
            $table->id();
            $table->string('company')->nullable();
            $table->string('contactPerson')->nullable();
            $table->string('comAddress')->nullable();
            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('postalCode')->nullable();
            $table->string('comEmail')->nullable();
            $table->string('comPhone')->nullable();
            $table->string('comMobile')->nullable();
            $table->string('comFax')->nullable();
            $table->string('comWebsite')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings_companies');
    }
}
