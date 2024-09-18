<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->string('status')->nullable()->after('netPayTotal');
            $table->string('savings')->nullable()->after('advance');
            $table->string('sssLoan')->nullable()->after('savings');
            $table->string('hmo')->nullable()->after('sssLoan');
            $table->string('reimbursement')->nullable()->after('bonus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('savings');
            $table->dropColumn('sssLoan');
            $table->dropColumn('hmo');
            $table->dropColumn('reimbursement');
        });
    }
}
