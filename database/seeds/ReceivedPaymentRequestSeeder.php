<?php

use Illuminate\Database\Seeder;

class ReceivedPaymentRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('received_payment_requests')->insert([
            'user_id' => 1,
            'payment_request_id' => 6,
        ]);
        DB::table('received_payment_requests')->insert([
            'user_id' => 1,
            'payment_request_id' => 7,
        ]);
        DB::table('received_payment_requests')->insert([
            'user_id' => 1,
            'payment_request_id' => 8,
        ]);
        DB::table('received_payment_requests')->insert([
            'user_id' => 2,
            'payment_request_id' => 9,
        ]);
        DB::table('received_payment_requests')->insert([
            'user_id' => 2,
            'payment_request_id' => 10,
        ]);
        DB::table('received_payment_requests')->insert([
            'user_id' => 2,
            'payment_request_id' => 11,
        ]);
    }
}
