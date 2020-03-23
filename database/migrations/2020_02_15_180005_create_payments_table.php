<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('payer_name')->nullable();
            $table->string('description');
            $table->decimal('amount', 9, 2);
            $table->string('currency');
            $table->enum('type', ['full', 'plan_payment', 'donation']);
            $table->string('note')->nullable();
            $table->string('image_path')->nullable();
            $table->datetime('paid_at')->nullable();
            $table->string('payment_id')->nullable();
            $table->unsignedBigInteger('bank_account_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('payment_request_id')->nullable();
            $table->timestamps();

            $table->foreign('bank_account_id')
                ->references('id')->on('bank_accounts');
            $table->foreign('user_id')
                ->references('id')->on('users');
            $table->foreign('payment_request_id')
                ->references('id')->on('payment_requests');
            $table->foreign('currency')
                ->references('currency')->on('currencies');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment');
    }
}
