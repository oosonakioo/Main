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
        Schema::create('news', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('news_category_id')->unsigned();
            $table->dateTime('news_date');
            $table->string('images', 250);
            $table->boolean('active')->default(false);
            $table->boolean('pin_home_page')->default(false);
            $table->integer('sort')->unsigned();
            $table->timestamps();
        });

        Schema::create('news_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('news_id')->unsigned();
            $table->string('title', 500);
            $table->text('detail');
            $table->string('locale')->index();

            $table->unique(['news_id', 'locale']);
            $table->foreign('news_id')->references('id')->on('news')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('news_translations');
        Schema::drop('news');
    }
};
