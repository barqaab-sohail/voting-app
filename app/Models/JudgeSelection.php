<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JudgeSelection extends Model
{
    use HasFactory;

    protected $fillable = ['vote_id', 'selected_judge_id'];

    public function vote()
    {
        return $this->belongsTo(Vote::class);
    }

    public function selectedJudge()
    {
        return $this->belongsTo(User::class, 'selected_judge_id');
    }
}
