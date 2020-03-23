<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('nr_of_payments');
            $table->unsignedBigInteger('payment_request_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('payment_request_id')->references('id')->on('payment_requests');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_plan');
    }
}
