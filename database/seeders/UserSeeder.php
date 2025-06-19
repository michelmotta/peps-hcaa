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
                'file_id' => 9,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'João Coordenador',
                'email' => 'coordenador@ufms.br',
                'expertise' => 'Cardiologista',
                'cpf' => '163.127.940-83',
                'username' => 'coordenador',
                'active' => true,
                'biography' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam consequat, nibh dignissim luctus consectetur, felis leo viverra sapien, id porttitor lorem est et lectus. Phasellus erat augue, tincidunt quis tortor ac, blandit pretium erat. Vestibulum placerat sapien turpis, at blandit turpis facilisis id. Suspendisse nunc dui, facilisis at egestas ac, viverra et massa.',
                'password' =>  Hash::make('505426'),
                'file_id' => 9,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'Pedro Professor',
                'email' => 'professor@ufms.br',
                'expertise' => 'Cardiologista',
                'cpf' => '969.944.580-75',
                'username' => 'professor',
                'active' => true,
                'biography' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam consequat, nibh dignissim luctus consectetur, felis leo viverra sapien, id porttitor lorem est et lectus. Phasellus erat augue, tincidunt quis tortor ac, blandit pretium erat. Vestibulum placerat sapien turpis, at blandit turpis facilisis id. Suspendisse nunc dui, facilisis at egestas ac, viverra et massa.',
                'password' =>  Hash::make('505426'),
                'file_id' => 9,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
            [
                'name' => 'Maria Estudante',
                'email' => 'estudante@ufms.br',
                'cpf' => '693.672.990-87',
                'username' => 'estudante',
                'active' => true,
                'password' =>  Hash::make('505426'),
                'file_id' => 9,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
        ];

        foreach ($users as $user) {
            User::factory()->create($user);
        }

        $files = File::all();
        
        foreach ($files as $file) {
            $file->user_id = 1;
            $file->save();
        }

    }
}
