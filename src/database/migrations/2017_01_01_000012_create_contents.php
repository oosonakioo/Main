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
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('menu', 50)->index();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('contents_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contents_id')->unsigned();
            $table->string('title', 250);
            $table->text('detail');
            $table->string('locale')->index();
            $table->unique(['contents_id', 'locale']);
            $table->foreign('contents_id')->references('id')->on('contents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('contents_translations');
        Schema::drop('contents');
    }
};
