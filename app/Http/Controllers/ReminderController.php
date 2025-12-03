<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    /**
     * Display a listing of the user's reminders.
     */
    public function index()
    {
        $reminders = Reminder::where('user_id', Auth::id())
            ->orderBy('is_completed', 'asc')
            ->orderBy('reminder_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reminders);
    }

    /**
     * Store a newly created reminder.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'priority' => 'required|in:low,medium,high',
            'reminder_date' => 'nullable|date',
            'text' => 'nullable|string|max:500',
        ]);

        $reminder = Reminder::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority ?? 'medium',
            'text' => $request->text ?? $request->title,
            'reminder_date' => $request->reminder_date,
            'is_completed' => false
        ]);

        return response()->json([
            'message' => 'Reminder created successfully!',
            'reminder' => $reminder
        ], 201);
    }

    /**
     * Update the specified reminder.
     */
    public function update(Request $request, Reminder $reminder)
    {
        // Ensure user owns this reminder
        if ($reminder->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'priority' => 'required|in:low,medium,high',
            'reminder_date' => 'nullable|date',
            'text' => 'nullable|string|max:500',
        ]);

        $reminder->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'text' => $request->text ?? $request->title,
            'reminder_date' => $request->reminder_date
        ]);

        return response()->json([
            'message' => 'Reminder updated successfully!',
            'reminder' => $reminder
        ]);
    }

    /**
     * Toggle the completion status of a reminder.
     */
    public function toggle(Reminder $reminder)
    {
        // Ensure user owns this reminder
        if ($reminder->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reminder->update([
            'is_completed' => !$reminder->is_completed
        ]);

        return response()->json([
            'message' => 'Reminder status updated!',
            'reminder' => $reminder
        ]);
    }

    /**
     * Remove the specified reminder.
     */
    public function destroy(Reminder $reminder)
    {
        // Ensure user owns this reminder
        if ($reminder->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reminder->delete();

        return response()->json([
            'message' => 'Reminder deleted successfully!'
        ]);
    }
}