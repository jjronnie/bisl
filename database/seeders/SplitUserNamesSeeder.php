<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SplitUserNamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Split existing user names into first_name and last_name
     *
     * Note: SMS settings are now app-wide and managed separately via database seed
     */
    public function run(): void
    {
        User::whereNull('first_name')->get()->each(function ($user) {
            $names = explode(' ', trim($user->name));

            if (count($names) >= 2) {
                // Take first as first_name, second as last_name (ignore extra names)
                $user->first_name = $names[0];
                $user->last_name = $names[1];
            } else {
                // If only one name, it's the first name
                $user->first_name = $names[0];
                $user->last_name = '';
            }

            $user->save();
        });
    }
}
