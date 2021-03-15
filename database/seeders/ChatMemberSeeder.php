<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $members = [
            ['chat_id' => 1, 'user_id' => 2],

            ['chat_id' => 2, 'user_id' => 1],
            
            ['chat_id' => 3, 'user_id' => 2],
            ['chat_id' => 3, 'user_id' => 4],
            
            ['chat_id' => 4, 'user_id' => 1],
            ['chat_id' => 4, 'user_id' => 3],
            
            ['chat_id' => 5, 'user_id' => 2],
        ];

        DB::table('chat_members')->insert($members);
    }
}
