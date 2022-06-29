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
            $table->string('text_color')->default('#FFFFFF');
            $table->string('border_color')->default('#FFFFFF');
            $table->string('points_color')->default('#FFFFFF');
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
            $table->dropColumn('text_color');
            $table->dropColumn('border_color');
            $table->dropColumn('points_color');
        });
    }
};
