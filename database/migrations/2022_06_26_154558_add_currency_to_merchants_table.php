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
        Schema::table('merchants', function (Blueprint $table) {
            $table->unsignedTinyInteger('loyalty_type')->default(0);
            $table->string('currency')->default('USD');
            $table->unsignedMediumInteger('loyalty_value')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn('loyalty_type');
            $table->dropColumn('currency');
            $table->dropColumn('loyalty_value');
        });
    }
};
