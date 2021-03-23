<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ChatMessage extends Model
{
    use HasFactory;

    public $timestamps = FALSE;

    protected $fillable = [
        'chat_id',
        'user_id',
        'content',
        'is_read',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    // public function getCreatedAtAttribute($value)
    // {
    //     return Carbon::parse($value)->tz('Europe/Moscow');
    // }
}
