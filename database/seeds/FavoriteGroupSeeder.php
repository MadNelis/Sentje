<?php

use Illuminate\Database\Seeder;

class FavoriteGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('favorites_groups')->insert([
            'name' => 'Lieve mensen',
            'user_id' => 1,
        ]);
        DB::table('favorites_groups')->insert([
            'name' => 'Parasieten',
            'user_id' => 1,
        ]);
        DB::table('favorites_groups')->insert([
            'name' => 'Lieve mensen',
            'user_id' => 2,
        ]);

        DB::table('favorites_group_members')->insert([
            'favorites_group_id' => 1,
            'user_id' => 5,
        ]);
        DB::table('favorites_group_members')->insert([
            'favorites_group_id' => 1,
            'user_id' => 6,
        ]);
        DB::table('favorites_group_members')->insert([
            'favorites_group_id' => 1,
            'user_id' => 7,
        ]);

        DB::table('favorites_group_members')->insert([
            'favorites_group_id' => 2,
            'user_id' => 8,
        ]);
        DB::table('favorites_group_members')->insert([
            'favorites_group_id' => 2,
            'user_id' => 9,
        ]);
        DB::table('favorites_group_members')->insert([
            'favorites_group_id' => 2,
            'user_id' => 10,
        ]);

        DB::table('favorites_group_members')->insert([
            'favorites_group_id' => 3,
            'user_id' => 5,
        ]);
        DB::table('favorites_group_members')->insert([
            'favorites_group_id' => 3,
            'user_id' => 7,
        ]);
        DB::table('favorites_group_members')->insert([
            'favorites_group_id' => 3,
            'user_id' => 10,
        ]);
    }
}
