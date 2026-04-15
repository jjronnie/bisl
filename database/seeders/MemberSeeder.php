<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = ['John', 'Mary', 'James', 'Sarah', 'Michael', 'Grace', 'David', 'Faith', 'Peter', 'Anna', 'Joseph', 'Ruth', 'Daniel', 'Esther', 'Samuel', 'Rebecca', 'Stephen', 'Rachel', 'Paul', 'Deborah'];
        $lastNames = ['Mukasa', 'Nakato', 'Sserwadda', 'Nabirye', 'Kaguta', 'Apio', 'Mwesigye', 'Namuli', 'Tumusiime', 'Katusiime', 'Atim', 'Amumpi', 'Okello', 'Opiyo', 'Wanyama', 'Nanjala', 'Oceng', 'Akampurira', 'Twinomujuni', 'Agaba'];

        $phonePrefixes = ['772', '780', '790', '747', '752', '730', '766'];
        $genders = ['male', 'female'];
        $tiers = ['silver', 'gold'];
        $maritalStatuses = ['single', 'married', 'divorced', 'widowed'];

        for ($i = 0; $i < 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $name = $firstName.' '.$lastName;
            $gender = $genders[array_rand($genders)];
            $phone = '+256'.$phonePrefixes[array_rand($phonePrefixes)].rand(100000, 999999);
            $email = strtolower(Str::slug($name, '.')).($i + 1).'@example.com';

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'status' => 'active',
                'must_change_password' => false,
            ]);
            $user->assignRole('user');

            $member = Member::create([
                'user_id' => $user->id,
                'name' => $name,
                'phone1' => $phone,
                'gender' => $gender,
                'tier' => $tiers[array_rand($tiers)],
                'nationality' => 'Ugandan',
                'marital_status' => $maritalStatuses[array_rand($maritalStatuses)],
                'date_of_birth' => now()->subYears(rand(20, 60))->subDays(rand(0, 365)),
            ]);

            SavingsAccount::create([
                'member_id' => $member->id,
                'account_number' => generateAccountNumber(),
                'balance' => rand(50000, 5000000),
                'loan_protection_fund' => rand(10000, 500000),
                'membership_fee' => 50000,
                'interest_earned' => rand(0, 100000),
                'status' => 'active',
            ]);
        }
    }
}
