<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInviteListEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_invite_list', function (Blueprint $table) {
            $table->integer('invite_list_id')->unsigned();
            $table->integer('event_id')->unsigned();

            $table->foreign('invite_list_id')->references('id')->on('invite_lists');
            $table->foreign('event_id')->references('id')->on('events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('event_invite_list');
    }
}
