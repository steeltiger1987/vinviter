<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('notification_type');

            $table->integer('link_user_id')->unsigned()->nullable();
            $table->foreign('link_user_id')->references('id')->on('users');

            $table->integer('link_event_id')->unsigned()->nullable();
            $table->foreign('link_event_id')->references('id')->on('events');

            $table->integer('link_page_id')->unsigned()->nullable();
            $table->foreign('link_page_id')->references('id')->on('pages');

            $table->boolean('seen')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('notifications');
    }
}
