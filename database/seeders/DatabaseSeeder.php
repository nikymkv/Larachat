<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(5)->create()->each(function($user) {
            $user->userChats()->create([
                'created_user_id' => $user->id,
                'type' => rand(1, 2) == 2 ? 'dialog' : 'public',
                'last_message' => 'Создан чат',
            ]);
        });

        $this->call(ChatMemberSeeder::class);
    }
}
