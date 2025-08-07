<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('categories_id')->unsigned();
            $table->string('menu', 50);
            $table->string('value', 250)->nullable();
            $table->text('option')->nullable();
            $table->string('image', 250)->nullable();
            $table->integer('sort');
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        Schema::create('products_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('products_id')->unsigned();
            $table->string('title', 250);
            $table->text('detail');
            $table->string('locale')->index();
            $table->unique(['products_id', 'locale']);
            $table->foreign('products_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('products_translations');
        Schema::drop('products');
    }
}
