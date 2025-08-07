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
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('custcode')->unsigned();
            $table->integer('custid')->unsigned();
            $table->integer('custgroupcode')->unsigned();
            $table->string('custnameeng', 250);
            $table->text('custadd')->nullable();
            $table->string('contfax', 250);
            $table->string('contactname', 250);
            $table->string('contemail', 250);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('students');
    }
};
