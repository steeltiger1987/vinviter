<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInviteListPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invite_list_page', function (Blueprint $table) {
            $table->integer('invite_list_id')->unsigned();
            $table->integer('page_id')->unsigned();

            $table->foreign('invite_list_id')->references('id')->on('invite_lists');
            $table->foreign('page_id')->references('id')->on('pages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('invite_list_page');
    }
}
