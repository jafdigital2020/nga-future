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
            // Add columns only if they do not already exist
            if (!Schema::hasColumn('users', 'fName')) {
                $table->string('fName')->nullable()->after('name');
            }
    
            if (!Schema::hasColumn('users', 'mName')) {
                $table->string('mName')->nullable()->after('fName');
            }
    
            if (!Schema::hasColumn('users', 'lName')) {
                $table->string('lName')->nullable()->after('mName');
            }
    
            if (!Schema::hasColumn('users', 'suffix')) {
                $table->string('suffix')->nullable()->after('lName');
            }
    
            if (!Schema::hasColumn('users', 'tin')) {
                $table->string('tin')->nullable()->after('philHealth');
            }
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
