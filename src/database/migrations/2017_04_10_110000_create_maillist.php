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
    public function up()
    {
        Schema::create('maillists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mailfrom', 250);
            $table->string('mailreplyto', 250);
            $table->string('mailto', 250);
            $table->string('mailcc', 250);
            $table->string('mailsubject', 250);
            $table->text('mailbody');
            $table->string('docuno', 50);
            $table->dateTime('docudate');
            $table->dateTime('shipdate');
            $table->integer('custcode')->unsigned();
            $table->string('custname', 250);
            $table->integer('templates_id')->unsigned();
            $table->string('remark', 250);
            $table->integer('sumtotal')->unsigned();
            $table->string('attach_pdf', 150);
            $table->string('attach_jpg', 150);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('maillists');
    }
};
