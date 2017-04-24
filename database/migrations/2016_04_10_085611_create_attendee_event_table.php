<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendeeEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendee_event', function (Blueprint $table)
        {
            $table->integer('event_id')->unsigned()->index();
            $table->foreign('event_id')->references('id')->on('events');

            $table->integer('attendee_id')->unsigned()->index();
            $table->foreign('attendee_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('attendee_event');
    }
}
