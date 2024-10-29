<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEarningListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('earning_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['fixed', 'percentage']); 
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
        Schema::dropIfExists('earning_lists');
    }
}
