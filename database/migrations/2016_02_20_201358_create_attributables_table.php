<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('attributables');
        Schema::create('attributables', function (Blueprint $table) {
            $table->integer('attribute_id')->unsigned();
            $table->integer('attributable_id')->unsigned();
            $table->string('attributable_type');

            $table->foreign('attribute_id')->references('id')->on('attributes');
            $table->primary(['attribute_id', 'attributable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('attributables');
    }
}
