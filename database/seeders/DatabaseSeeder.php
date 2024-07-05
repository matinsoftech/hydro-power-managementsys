<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 'password',
            'user_type' => 'Admin',
            'email_verified_at' => now(),
        ]);

        \App\Models\SiteSetting::create([
            'name' => 'Hydropower',
            'logo' => 'assets/images/freedashDark.svg',
            'email' => 'admin@gmail.com',
            'phone' => '9800930444',
            'address' => 'Biratnagar 05, Kanchanbari, Nepal',
            'description' => 'Website Description.',
            'keywords' => 'Website Keywords.',
        ]);

        \App\Models\MailConfig::create([
            'driver' => 'smtp',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'encryption' => 'tls',
            'username' => 'admin@gmail.com',
            'password' => 'password',
            'from_address' => 'Admin',
            'from_name' => 'admin@gmail.com',
        ]);
    }
}
