<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('merchant_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade')->onUpdate('cascade');
            $table->string('title');
            $table->string('description');
            $table->integer('value');
            $table->string('photo');
            $table->dateTime('valid_until')->useCurrent();
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
        Schema::dropIfExists('rewards');
    }
};
