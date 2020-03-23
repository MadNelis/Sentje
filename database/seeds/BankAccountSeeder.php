<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;


class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds. Inserts 10 bankaccounts.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bank_accounts')->insert([
            'name' => 'ING Rekening',
            'iban' => Crypt::encryptString('NL94INGB4745657714'),
            'user_id' => 1,
        ]);
        DB::table('bank_accounts')->insert([
            'name' => 'ABN Rekening',
            'iban' => Crypt::encryptString('NL51ABNA3115404417'),
            'user_id' => 1,
        ]);
        DB::table('bank_accounts')->insert([
            'name' => 'SNS Rekening',
            'iban' => Crypt::encryptString('NL07INGB4824507324'),
            'user_id' => 1,
        ]);
        DB::table('bank_accounts')->insert([
            'name' => 'PayPal',
            'iban' => Crypt::encryptString('NL37ABNA8430666915'),
            'user_id' => 2,
        ]);
        DB::table('bank_accounts')->insert([
            'name' => 'Rabobank',
            'iban' => Crypt::encryptString('NL52RABO9893570476'),
            'user_id' => 2,
        ]);
        DB::table('bank_accounts')->insert([
            'name' => 'Fortis',
            'iban' => Crypt::encryptString('NL24RABO6502761380'),
            'user_id' => 3,
        ]);
        DB::table('bank_accounts')->insert([
            'name' => 'ING Rekening',
            'iban' => Crypt::encryptString('NL45INGB6121940131'),
            'user_id' => 3,
        ]);
        DB::table('bank_accounts')->insert([
            'name' => 'ABN Studentenrekening',
            'iban' => Crypt::encryptString('NL40ABNA9154010896'),
            'user_id' => 4,
        ]);
        DB::table('bank_accounts')->insert([
            'name' => 'SNS Bank',
            'iban' => Crypt::encryptString('NL62INGB4649075238'),
            'user_id' => 4,
        ]);
        DB::table('bank_accounts')->insert([
            'name' => 'Van Landschot',
            'iban' => Crypt::encryptString('NL56INGB2555569839'),
            'user_id' => 5,
        ]);
    }
}
