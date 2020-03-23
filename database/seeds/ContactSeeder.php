<?php

use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_contacts')->insert([
            'user_id' => 1,
            'contact_id' => 2,
        ]);
        DB::table('user_contacts')->insert([
            'user_id' => 1,
            'contact_id' => 3,
        ]);
    }
}
