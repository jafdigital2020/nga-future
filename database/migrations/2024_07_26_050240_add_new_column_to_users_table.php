<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('fName')->nullable()->after('name');
            $table->string('mName')->nullable()->after('fName');
            $table->string('lName')->nullable()->after('mName');
            $table->string('suffix')->nullable()->after('lName');
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
            $table->dropColumn('fName');
            $table->dropColumn('mName');
            $table->dropColumn('lName');
            $table->dropColumn('suffix');
            $table->dropColumn('tin');
        });
    }
}
