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
        Schema::create('templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mailfrom', 250);
            $table->string('mailreplyto', 250);
            $table->string('mailto', 250);
            $table->string('mailcc', 250);
            $table->string('mailsubject', 250);
            $table->text('mailbody');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::drop('templates');
    }
};
