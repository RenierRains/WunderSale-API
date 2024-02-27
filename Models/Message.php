<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['body', 'user_id', 'conversation_id'];

    // Relationship with User
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relationship with Conversation
    public function conversation() {
        return $this->belongsTo(Conversation::class);
    }
}
