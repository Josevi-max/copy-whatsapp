<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatUser extends Model
{
    use HasFactory;

    public function user() {
        return $this->belongsToMany(User::class);
    }

    public function chats() {
        return $this->belongsToMany(Chat::class)
        ->withPivot('color','active')->withTimestamps();
    }
}
