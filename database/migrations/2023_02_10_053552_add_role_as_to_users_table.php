<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleAsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('role_as')->default('0');
            $table->string('empNumber')->nullable()->after('password');
            $table->string('typeOfContract')->nullable()->after('empNumber');
            $table->string('phoneNumber')->nullable()->after('typeOfContract');
            $table->string('dateHired')->nullable()->after('phoneNumber');
            $table->string('birthday')->nullable()->after('dateHired');
            $table->string('completeAddress')->nullable()->after('birthday');
            $table->string('position')->nullable()->after('completeAddress');
            $table->string('department')->nullable()->after('position');
            $table->string('image')->nullable()->after('department');
            $table->string('sss')->nullable()->after('image');
            $table->string('pagIbig')->nullable()->after('sss');
            $table->string('philHealth')->nullable()->after('pagIbig');
            $table->string('tin')->nullable()->after('philHealth');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('role_as');
            $table->string('empNumber');
            $table->string('typeOfContract');
            $table->string('phoneNumber');
            $table->string('dateHired');
            $table->string('birthday');
            $table->string('completeAddress');
            $table->string('position');
            $table->string('image');
            $table->string('sss');
            $table->string('pagIbig');
            $table->string('philHealth');
        });
    }
}
