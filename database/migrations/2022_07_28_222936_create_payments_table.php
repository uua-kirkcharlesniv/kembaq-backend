<?php

use App\Models\Merchant;
use App\Models\Payment;
use App\Models\Subscription;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('merchant_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('type'); // 0 - trial, 1 - starter, 2 - business, 3 - corporate
            $table->integer('recurring'); // 0 - trial, 1 - monthly, 2 - annual
            $table->string('amount_paid');
            $table->timestamps();
        });

        $merchants = Merchant::all();

        foreach ($merchants as $merchant) {
            Payment::create([
                'merchant_id' => $merchant->id,
                'type' => 0,
                'recurring' => 0,
                'amount_paid' => '0',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
