<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'text',
        'reminder_date',
        'is_completed'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'reminder_date' => 'datetime'
    ];

    /**
     * Get the user that owns the reminder.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include reminders for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include incomplete reminders.
     */
    public function scopeIncomplete($query)
    {
        return $query->where('is_completed', false);
    }

    /**
     * Scope a query to only include completed reminders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope a query to only include reminders with dates.
     */
    public function scopeWithDate($query)
    {
        return $query->whereNotNull('reminder_date');
    }

    /**
     * Scope a query to only include upcoming reminders.
     */
    public function scopeUpcoming($query)
    {
        return $query->whereNotNull('reminder_date')
            ->where('reminder_date', '>=', today())
            ->where('is_completed', false)
            ->orderBy('reminder_date', 'asc');
    }
}