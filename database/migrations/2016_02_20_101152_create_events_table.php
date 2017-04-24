<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('events');
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('title');
            $table->string('address');
            $table->string('zip_code', 30);
            $table->text('details');

            $table->integer('venue_page_id')->unsigned()->nullable();
            $table->integer('creator_page_id')->unsigned()->nullable();

            $table->foreign('venue_page_id')->references('id')->on('pages');
            $table->foreign('creator_page_id')->references('id')->on('pages');

            $table->integer('city_id')->unsigned();
            $table->integer('region_id')->unsigned();
            $table->integer('country_id')->unsigned();

            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('region_id')->references('id')->on('regions');
            $table->foreign('country_id')->references('id')->on('countries');

            $table->boolean('is_location_hidden')->default(false);
            $table->boolean('is_private')->default(false);
            $table->boolean('published')->default(false);

            $table->timestamp('starts_at')->default('0000-00-00');
            $table->timestamp('ends_at')->default('0000-00-00');
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
        Schema::drop('events');
    }
}
