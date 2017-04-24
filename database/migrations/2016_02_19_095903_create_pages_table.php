<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('address');
            $table->string('zip_code', 30);
            $table->text('status');
            $table->text('story');

            $table->string('main_image')->nullable();
            $table->string('background_image')->nullable();

            $table->integer('city_id')->unsigned();
            $table->integer('region_id')->unsigned();
            $table->integer('country_id')->unsigned();

            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('region_id')->references('id')->on('regions');
            $table->foreign('country_id')->references('id')->on('countries');

            $table->index('city_id');
            $table->index('region_id');
            $table->index('country_id');

            $table->boolean('published')->default(false);

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
        Schema::drop('pages');
    }
}
