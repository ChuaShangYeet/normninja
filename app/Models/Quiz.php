<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'subject',
        'duration_minutes',
        'passing_score',
        'is_published',
        'available_from',
        'available_until',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'duration_minutes' => 'integer',
        'passing_score' => 'integer',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function getTotalPointsAttribute()
    {
        return $this->questions()->sum('points');
    }

    // Scope for active quizzes
    public function scopeActive($query)
    {
        return $query->where('is_published', true)
                    ->where(function($q) {
                        $q->whereNull('available_from')
                        ->orWhere('available_from', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('available_until')
                        ->orWhere('available_until', '>=', now());
                    });
    }

    public function isActive()
    {
        if (!$this->is_published) return false;
        if ($this->available_from && $this->available_from > now()) return false;
        if ($this->available_until && $this->available_until < now()) return false;
        return true;
    }

    public function statusForStudent(User $student): string
    {
        $attempt = $this->attempts()
            ->where('student_id', $student->id)
            ->where('is_completed', true)
            ->first();

        if ($attempt) {
            return 'completed';
        }

        if ($this->available_until && now()->gt($this->available_until)) {
            return 'missed';
        }

        if (!$this->isActive()) {
            return 'locked';
        }

        return 'available';
    }


}
