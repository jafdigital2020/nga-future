<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\OvertimeCredits;
use Illuminate\Database\Seeder;

class OvertimeCreditsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            OvertimeCredits::create([
                'users_id' => $user->id,
                'otCredits' => '16:00:00',
            ]);
        }
    }
}
