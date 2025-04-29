<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'user_id', 'vote_choice'];

    public function session()
    {
        return $this->belongsTo(VotingSession::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function judgeSelections()
    {
        return $this->hasMany(JudgeSelection::class);
    }
}
