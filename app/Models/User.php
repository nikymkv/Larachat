<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function rooms()
    {
        return $this->belongsToMany(Room::class);
    }

    public function userChats()
    {
        return $this->belongsToMany(Chat::class, 'chat_members');
    }

    public function containChat($chat_id)
    {
        $chat = User::join('chat_members', 'chat_members.user_id', '=', 'users.id')
                    ->where('users.id', '=' , $this->id)
                    ->where('chat_members.chat_id', '=' , $chat_id)
                    ->select(['chat_members.chat_id'])
                    ->limit(1)
                    ->get()
                    ->first();
                    
        return $chat == null ? false : true;
    }
}
