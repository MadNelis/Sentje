<?php

use Illuminate\Database\Seeder;

class PaymentRequestSeeder extends Seeder
{
    /**
     * Run the database seeds. Inserts 11 paymentrequests.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_requests')->insert([
            'description' => 'Etentje',
            'amount' => 999.0,
            'currency' => 'EUR',
            'bank_account_id' => 1,
        ]);
        DB::table('payment_requests')->insert([
            'description' => 'Karten',
            'amount' => 10.0,
            'currency' => 'EUR',
            'bank_account_id' => 1,
        ]);
        DB::table('payment_requests')->insert([
            'description' => 'Bier',
            'amount' => 5.50,
            'currency' => 'EUR',
            'bank_account_id' => 2,
        ]);
        DB::table('payment_requests')->insert([
            'description' => 'Boodschappen',
            'amount' => 55.73,
            'currency' => 'EUR',
            'bank_account_id' => 2,
        ]);
        DB::table('payment_requests')->insert([
            'description' => 'Vakantie',
            'amount' => 280.0,
            'currency' => 'EUR',
            'bank_account_id' => 3,
        ]);
        DB::table('payment_requests')->insert([
            'description' => 'Pizza',
            'amount' => 7.70,
            'currency' => 'EUR',
            'bank_account_id' => 4,
        ]);
        DB::table('payment_requests')->insert([
            'description' => 'Taxi',
            'amount' => 15.0,
            'currency' => 'EUR',
            'bank_account_id' => 5,
        ]);
        DB::table('payment_requests')->insert([
            'description' => 'Ticket',
            'amount' => 60.0,
            'currency' => 'EUR',
            'bank_account_id' => 6,
        ]);
        DB::table('payment_requests')->insert([
            'description' => 'Yacht',
            'amount' => 2000000.20,
            'currency' => 'EUR',
            'bank_account_id' => 7,
        ]);
        DB::table('payment_requests')->insert([
            'description' => 'Laptop',
            'amount' => 3000.0,
            'currency' => 'EUR',
            'bank_account_id' => 8,
        ]);
        DB::table('payment_requests')->insert([
            'description' => 'Fiets',
            'amount' => 2.50,
            'currency' => 'EUR',
            'bank_account_id' => 8,
        ]);
    }
}
