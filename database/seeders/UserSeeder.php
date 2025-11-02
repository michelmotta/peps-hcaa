<?php

namespace Database\Seeders;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Michel Motta da Silva',
                'email' => 'michel.motta@ufms.br',
                'expertise' => 'Desenvolvedor',
                'cpf' => '044.484.181-46',
                'username' => 'michel.motta',
                'active' => true,
                'biography' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam consequat, nibh dignissim luctus consectetur, felis leo viverra sapien, id porttitor lorem est et lectus. Phasellus erat augue, tincidunt quis tortor ac, blandit pretium erat. Vestibulum placerat sapien turpis, at blandit turpis facilisis id. Suspendisse nunc dui, facilisis at egestas ac, viverra et massa.',
                'password' =>  Hash::make('505426'),
                'file_id' => null,
                'sector_id' => 1,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'Paula Riccio Barbosa',
                'email' => 'paula81riccio@gmail.com',
                'cpf' => '000.000.000-00',
                'username' => 'paula.riccio',
                'active' => true,
                'password' =>  Hash::make('@paula123@'),
                'file_id' => null,
                'sector_id' => 1,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        ];

        foreach ($users as $user) {
            User::factory()->create($user);
        }
    }
}
