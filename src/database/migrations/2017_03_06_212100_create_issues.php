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
        Schema::create('issues', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('issue_topic_id')->unsigned();
            $table->string('issue', 500);
            $table->string('name', 500);
            $table->string('company', 500);
            $table->string('tel', 50);
            $table->string('email', 100);
            $table->string('detail', 2000);
            $table->boolean('read')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->dropColumn('deleted_at');
        Schema::drop('issues');
    }
};
