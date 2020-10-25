<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $username = 'Florian';

        $userId = DB::table('users')->insertGetId([
            'name' => $username,
            'email' => 'test@email.com',
            'email_verified_at' => date('c'),
            'password' => bcrypt('P@ssw0rd'),
            'sex' => 'male',
            'birthdate' => date('c'),
        ]);

        DB::table('teams')->insert([
            'user_id' => $userId,
            'name' => explode(' ', $username, 2)[0]."'s Team",
            'personal_team' => true,

        ]);


        $username = 'Michalis';

        $userId = DB::table('users')->insertGetId([
            'name' => $username,
            'email' => 'test2@email.com',
            'email_verified_at' => date('c'),
            'password' => bcrypt('P@ssw0rd'),
            'sex' => 'male',
            'birthdate' => date('c'),
        ]);

        DB::table('teams')->insert([
            'user_id' => $userId,
            'name' => explode(' ', $username, 2)[0]."'s Team",
            'personal_team' => true,

        ]);

        $username = 'Thorben';

        $userId = DB::table('users')->insertGetId([
            'name' => $username,
            'email' => 'test3@email.com',
            'email_verified_at' => date('c'),
            'password' => bcrypt('P@ssw0rd'),
            'sex' => 'male',
            'birthdate' => date('c'),
        ]);

        DB::table('teams')->insert([
            'user_id' => $userId,
            'name' => explode(' ', $username, 2)[0]."'s Team",
            'personal_team' => true,

        ]);
    }
}
