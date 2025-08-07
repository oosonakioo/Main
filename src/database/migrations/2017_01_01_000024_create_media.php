<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('menu', 500);
            $table->string('images', 250);
            $table->string('videos', 500);
            $table->string('downloads', 500);
            $table->boolean('pin_home_page')->default(false);
            $table->boolean('active')->default(false);
            $table->integer('sort')->unsigned();
            $table->timestamps();
        });

        Schema::create('medias_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('medias_id')->unsigned();
            $table->string('title', 500);

            $table->string('locale')->index();
            $table->unique(['medias_id', 'locale']);
            $table->foreign('medias_id')->references('id')->on('medias')->onDelete('cascade');
        });

        Schema::create('medias_gallerys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('medias_id')->unsigned();
            $table->string('images', 250);
            $table->integer('sort')->unsigned();
            $table->timestamps();
            $table->foreign('medias_id')->references('id')->on('medias')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('medias_translations');
        Schema::drop('medias_gallerys');
        Schema::drop('medias');
    }
};
