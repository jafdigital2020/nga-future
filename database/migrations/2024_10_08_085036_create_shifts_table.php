<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('shift_name');
            $table->time('start_time');
            $table->time('late_threshold');
            $table->time('end_time');
            $table->integer('break_time')->nullable();  // In minutes
            $table->boolean('recurring')->default(false);
            $table->integer('repeat_every')->nullable(); // Repeat every X weeks
            $table->json('days')->nullable();  // JSON array for days of the week
            $table->date('end_on')->nullable();  // End date for recurrence
            $table->boolean('indefinite')->default(false); // Whether the shift is indefinite
            $table->string('tag')->nullable();  // A tag for the shift
            $table->text('note')->nullable();  // Optional note for the shift
            $table->timestamps();  // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shifts');
    }
}
