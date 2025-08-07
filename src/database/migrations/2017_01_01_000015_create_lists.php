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
        Schema::create('lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('menu', 150);
            $table->string('value', 250)->nullable();
            $table->text('option')->nullable();
            $table->string('image', 250)->nullable();
            $table->integer('sort')->unsigned();
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        Schema::create('lists_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lists_id')->unsigned();
            $table->string('title', 255);
            $table->text('detail');
            $table->string('locale')->index();

            $table->unique(['lists_id', 'locale']);
            $table->foreign('lists_id')->references('id')->on('lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('lists_translations');
        Schema::drop('lists');
    }
};
