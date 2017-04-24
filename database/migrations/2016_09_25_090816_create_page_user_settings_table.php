<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageUserSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_user_settings', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('page_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->boolean('receive_page_notifications')->default(FALSE);

            $table->foreign('page_id')->references('id')->on('pages');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('page_user_settings');
    }
}
