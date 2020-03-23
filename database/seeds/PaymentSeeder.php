<?php

use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payments')->insert([
            'payer_name'=> 'Harold',
            'description'=> 'Etentje',
            'amount' => 999.0,
            'currency' => 'EUR',
            'note' => 'Dat ene etentje witte wel',
            'paid_at' => now(),
            'payment_id' => '123456',
            'bank_account_id' => 1,
            'user_id' => null,
            'payment_request_id'=> 1,
        ]);
        DB::table('payments')->insert([
            'payer_name'=> 'Lisa',
            'description'=> 'Etentje',
            'amount' => 999.0,
            'currency' => 'EUR',
            'note' => 'Dat ene etentje ja',
            'paid_at' => now(),
            'payment_id' => '123458',
            'bank_account_id' => 1,
            'user_id' => null,
            'payment_request_id'=> 1,
        ]);
        DB::table('payments')->insert([
            'payer_name'=> 'Kees',
            'description'=> 'Etentje',
            'amount' => 999.0,
            'currency' => 'EUR',
            'note' => 'Kan me niet herinneren',
            'paid_at' => now(),
            'payment_id' => '123457',
            'bank_account_id' => 1,
            'user_id' => 3,
            'payment_request_id'=> 1,
        ]);
    }
}
