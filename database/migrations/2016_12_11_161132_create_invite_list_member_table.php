<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInviteListMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invite_list_member', function (Blueprint $table) {
            $table->integer('list_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->foreign('list_id')->references('id')->on('invite_lists');
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
        Schema::drop('invite_list_member');
    }
}
