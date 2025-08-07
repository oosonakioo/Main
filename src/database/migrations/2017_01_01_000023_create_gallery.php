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
        Schema::create('gallerys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gallerys_category_id')->unsigned();
            $table->string('menu', 50);
            $table->string('images', 50);
            $table->integer('sort');
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        Schema::create('gallerys_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gallerys_id')->unsigned();
            $table->string('title', 250);
            $table->text('detail');
            $table->string('locale')->index();
            $table->unique(['gallerys_id', 'locale']);
            $table->foreign('gallerys_id')->references('id')->on('gallerys')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('gallerys_translations');
        Schema::drop('gallerys');
    }
};
