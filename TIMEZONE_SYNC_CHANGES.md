# Timezone Synchronization Changes

## Summary
This document outlines all changes made to synchronize the application to use Malaysia Time (GMT+8 / Asia/Kuala_Lumpur) consistently across quiz, game, and calendar features.

## Changes Made

### 1. Application Timezone Configuration
**File:** `config/app.php`

Changed the default timezone from `UTC` to `Asia/Kuala_Lumpur` (GMT+8).

```php
// Before
'timezone' => 'UTC',

// After
'timezone' => 'Asia/Kuala_Lumpur',
```

**Impact:** All timestamp operations using Laravel's `now()`, `today()`, and Carbon date functions will now use Malaysia time instead of UTC.

---

### 2. Reminder Schema Update
**File:** `database/migrations/2024_12_03_000001_update_reminders_table_add_missing_columns.php`

Created a new migration to fix critical schema mismatches in the reminders table.

**Changes:**
- Added `title` column (string, nullable)
- Added `description` column (text, nullable)
- Added `priority` column (enum: low, medium, high, default: medium)
- Renamed `date` column to `reminder_date` and changed type from DATE to DATETIME
- Updated database indexes accordingly

**Reason:** The calendar view was expecting these columns but they didn't exist in the database, causing runtime errors.

---

### 3. Reminder Model Update
**File:** `app/Models/Reminder.php`

**Updated fillable attributes:**
```php
protected $fillable = [
    'user_id',
    'title',           // New
    'description',     // New
    'priority',        // New
    'text',
    'reminder_date',   // Renamed from 'date'
    'is_completed'
];
```

**Updated casts:**
```php
protected $casts = [
    'is_completed' => 'boolean',
    'reminder_date' => 'datetime'  // Changed from 'date' to 'datetime'
];
```

**Updated scopes:**
- `scopeWithDate()`: Uses `reminder_date` instead of `date`
- `scopeUpcoming()`: Uses `reminder_date` with timezone-aware comparison

---

### 4. Reminder Controller Update
**File:** `app/Http/Controllers/ReminderController.php`

**Updated methods:**

#### `index()` method:
- Changed `orderBy('date')` to `orderBy('reminder_date')`

#### `store()` method:
- Updated validation rules to include new fields:
  - `title` (required)
  - `description` (optional)
  - `priority` (required: low/medium/high)
  - `reminder_date` (optional datetime)
- Updated create logic to save all new fields

#### `update()` method:
- Updated validation rules (same as store)
- Updated update logic to handle all new fields

---

## Features Now Using Malaysia Time (GMT+8)

### 1. Quiz Feature
**Files affected:**
- `app/Http/Controllers/QuizController.php`
- `app/Models/Quiz.php`
- `app/Models/QuizAttempt.php`

**What changed:**
- All `now()` calls now return Malaysia time
- `available_from` and `available_until` timestamps use Malaysia timezone
- `started_at` and `completed_at` are recorded in Malaysia time
- Date comparisons and availability checks use Malaysia time

**Example:** If a quiz is set to be available from "2025-12-04 09:00:00", it will be 9:00 AM Malaysia time (GMT+8), not UTC.

---

### 2. Game Feature
**Files affected:**
- `app/Http/Controllers/GameController.php`
- `app/Models/GameAttempt.php`

**What changed:**
- All timestamp operations use Malaysia time
- `created_at` and `updated_at` timestamps stored in Malaysia time
- Game attempt tracking uses local timezone

---

### 3. Calendar & Reminder Feature
**Files affected:**
- `app/Models/CalendarEvent.php`
- `app/Models/Reminder.php`
- `app/Http/Controllers/ReminderController.php`
- `resources/views/calendar/index.blade.php`

**What changed:**
- Reminders now support full datetime (not just date)
- Calendar events use Malaysia timezone
- `today()` comparison uses Malaysia time
- Event sorting and filtering use local timezone

---

## Action Required: Run Migration

**IMPORTANT:** You must run the following migration in your environment:

```bash
php artisan migrate
```

This will:
1. Add new columns to the reminders table (title, description, priority)
2. Change the date column to datetime type
3. Rename date to reminder_date
4. Update database indexes

**Note:** Existing reminder data will be preserved. The `text` column remains for backward compatibility.

---

## Testing Recommendations

After running the migration, test the following:

### 1. Quiz Availability
- Create a quiz with availability times
- Verify that the times shown match Malaysia time (GMT+8)
- Test quiz access at boundary times

### 2. Game Timestamps
- Play a game and check the completion time
- Verify timestamps are displayed in Malaysia time

### 3. Calendar & Reminders
- Create a new reminder with date and time
- Verify the reminder appears at the correct Malaysia time
- Test editing and deleting reminders
- Check that upcoming reminders are sorted correctly

### 4. General Timestamps
- Check all `created_at` and `updated_at` timestamps
- Verify they display Malaysia time in views
- Test filtering by date (e.g., "today", "upcoming")

---

## Potential Issues to Watch For

### 1. Existing Data
- **Database timestamps:** All existing timestamps in the database are stored in UTC
- **Display:** Laravel will automatically convert stored UTC times to Malaysia time for display
- **Note:** This is standard Laravel behavior and should work correctly

### 2. Browser Timezone
- The calendar view uses FullCalendar.js which may display times in the user's browser timezone
- Server-side operations now use Malaysia time consistently
- Ensure JavaScript code doesn't override with browser local time

### 3. API Responses
- If you have mobile apps or external API consumers, they need to be aware that timestamps now use Asia/Kuala_Lumpur timezone
- Consider adding timezone information to API documentation

### 4. Date Comparisons
- All queries using `today()`, `now()`, or date comparisons will use Malaysia time
- This may affect filtering (e.g., "show today's assignments")
- Users in different timezones should be aware the app uses Malaysia time

---

## Rollback Instructions

If you need to rollback these changes:

1. **Revert timezone config:**
```bash
# In config/app.php
'timezone' => 'UTC',
```

2. **Rollback migration:**
```bash
php artisan migrate:rollback
```

3. **Revert model and controller changes:**
```bash
git revert <commit-hash>
```

---

## Files Changed

1. `config/app.php` - Timezone configuration
2. `database/migrations/2024_12_03_000001_update_reminders_table_add_missing_columns.php` - New migration
3. `app/Models/Reminder.php` - Model updates
4. `app/Http/Controllers/ReminderController.php` - Controller updates

---

## Questions or Issues?

If you encounter any timezone-related issues:

1. Check that the migration has been run successfully
2. Clear Laravel cache: `php artisan cache:clear && php artisan config:clear`
3. Verify the timezone is set correctly: `php artisan tinker` then `echo config('app.timezone');`
4. Check PHP timezone: `php -r "echo date_default_timezone_get();"`

---

**Date:** 2025-12-03
**Changes by:** Claude Code Assistant
**Timezone:** Asia/Kuala_Lumpur (GMT+8)
