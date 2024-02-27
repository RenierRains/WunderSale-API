<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['user_one', 'user_two'];

    // Relationship
    public function userOne() {
        return $this->belongsTo(User::class, 'user_one', 'id');
    }

    // Relationship 
    public function userTwo() {
        return $this->belongsTo(User::class, 'user_two', 'id');
    }

    // Relationship with Message 
    public function messages() {
        return $this->hasMany(Message::class);
    }
}
