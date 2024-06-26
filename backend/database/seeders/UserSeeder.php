<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'firstname' => 'Ugbanawaji',
            'lastname' => 'Ekenekiso',
            'middlename' => 'Leonard',
            'email' => 'developer@ugbanawaji.com',
            'password' => 'password',
        ]);

        $user->assignRoles('manager');
        UserProfile::factory()
            ->create([
                'user_id' => $user->id,
            ]);
        //        $user = User::factory()->create([
        //            'firstname' => 'Movies',
        //            'lastname' => 'Web',
        //            'email' => 'movieswebbs@gmail.com',
        //            'password' => 'password',
        //        ]);

        $user->assignRoles('developer');
        UserProfile::factory()
            ->create([
                'user_id' => $user->id,
            ]);
        Message::factory(20)
            ->create(['user_id' => $user->id]);
    }
}
