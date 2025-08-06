<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('main_id');
            $table->integer('parent_regions_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->string('menu', 50);
            $table->string('image', 250);
            $table->integer('sort');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('regions_translations', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('regions_id')->unsigned();
            $table->string('title', 250);
            $table->text('detail');
            $table->string('locale')->index();
            $table->unique(['regions_id','locale']);
            $table->foreign('regions_id')->references('id')->on('regions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('regions_translations');
        Schema::drop('regions');
    }
}
