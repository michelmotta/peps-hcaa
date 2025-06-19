<?php

namespace Database\Seeders;

use App\Enums\ProfileEnum;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProfileUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usernameToProfile = [
            'coordenador' => ProfileEnum::COORDENADOR,
            'professor' => ProfileEnum::PROFESSOR,
        ];

        User::all()->each(function ($user) use ($usernameToProfile) {
            if ($user->username === 'michel.motta') {
                // Assign all profiles to michel.motta
                $allProfiles = [
                    ProfileEnum::COORDENADOR->value,
                    ProfileEnum::PROFESSOR->value,
                ];

                // Assign all profiles
                $user->profiles()->sync($allProfiles);
                return;
            }

            // Assign specific profile based on username
            if (isset($usernameToProfile[$user->username])) {

                $profileId = $usernameToProfile[$user->username]->value;

                $user->profiles()->sync([$profileId]);
            }
        });
    }
}
