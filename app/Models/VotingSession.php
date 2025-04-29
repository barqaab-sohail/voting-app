<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VotingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'judges_count',
        'created_by',
        'start_time',
        'end_time',
        'is_active'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'session_id');
    }

    public function isActive()
    {
        return $this->is_active && now()->between($this->start_time, $this->end_time);
    }
}
