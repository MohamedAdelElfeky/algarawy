<?php

namespace App\Domain\Chat\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationParticipant extends Model
{
    use HasFactory;

    protected $fillable = ['conversation_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
