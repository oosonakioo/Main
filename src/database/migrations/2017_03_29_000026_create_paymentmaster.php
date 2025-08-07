<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paymentmasters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('docuno', 50);
            $table->dateTime('docudate');
            $table->dateTime('shipdate');
            $table->integer('custcode')->unsigned();
            $table->string('custnameeng', 250);
            $table->integer('templateno')->unsigned();
            $table->string('remark', 250);
            $table->integer('paymentstatus')->unsigned();
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
        Schema::drop('paymentmaster');
    }
}
