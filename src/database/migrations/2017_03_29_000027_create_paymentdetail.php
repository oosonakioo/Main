<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paymentdetails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('docuno_id')->unsigned()->nullable();
            $table->integer('listno')->unsigned();
            $table->integer('goodprice2')->unsigned();
            $table->integer('goodqty2')->unsigned();
            $table->string('goodcode', 250);
            $table->string('goodnameeng1', 250);
            $table->integer('rematotalamnt')->unsigned();
            $table->integer('gooddiscamnt')->unsigned();
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
        Schema::drop('paymentdetail');
    }
}
