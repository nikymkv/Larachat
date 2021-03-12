<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'created_user_id',
        'type',
    ];

    public function members()
    {
        return $this->belongsToMany(User::class, 'chat_members');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
