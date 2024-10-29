<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInlucsionColumnsToUserearningTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_earnings', function (Blueprint $table) {
            $table->integer('inclusion_count')->default(0)->after('earning_id'); // Track how many times it's been included in payroll
            $table->boolean('active')->default(1)->after('inclusion_count'); // Whether the earning is still active
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_earnings', function (Blueprint $table) {
            $table->dropColumn('inclusion_count');
            $table->dropColumn('active');
        });
    }
}
