<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('menu', 50);
            $table->string('value', 250)->nullable();
            $table->integer('sort');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('categories_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('categories_id')->unsigned();
            $table->string('title', 250);
            $table->text('detail');
            $table->string('locale')->index();
            $table->unique(['categories_id', 'locale']);
            $table->foreign('categories_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('categories_translations');
        Schema::drop('categories');
    }
};
