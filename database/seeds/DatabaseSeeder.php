<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CurrencySeeder::class,
            UsersTableSeeder::class,
            BankAccountSeeder::class,
            PaymentRequestSeeder::class,
            PaymentSeeder::class,
            ReceivedPaymentRequestSeeder::class,
            ContactSeeder::class,
            FavoriteGroupSeeder::class,
        ]);
    }
}
