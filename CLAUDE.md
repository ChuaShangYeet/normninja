# CLAUDE.md - AI Assistant Guide for NormNinja LMS

## Project Overview

**NormNinja** is a comprehensive Learning Management System (LMS) built with Laravel 12 and PHP 8.2+. It supports three user roles (Admin, Teacher, Student) with distinct capabilities for managing educational content, assessments, games, and student performance analytics.

**Key Technologies:**
- **Backend:** Laravel 12.0, PHP 8.2+
- **Database:** MySQL/PostgreSQL with 17 migrations
- **Frontend:** Blade templating, Tailwind CSS 4.0, Vite 7.0.7
- **Icons:** Font Awesome 6.4.0
- **Testing:** PHPUnit 11.5.3

**Domain:** Educational platform for teachers to create content and track student performance, students to access learning materials and take assessments, and admins to manage users.

---

## Project Structure

```
normninja/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # 11 controllers (Auth, Admin, Teacher, Student, Quiz, Game, Forum, etc.)
│   │   ├── Middleware/         # RoleMiddleware.php for role-based access
│   │   └── Kernel.php          # Middleware aliases
│   ├── Models/                 # 14 Eloquent models (User, Quiz, Game, Forum, Assignment, etc.)
│   ├── Policies/               # 4 authorization policies (QuizPolicy, GamePolicy, etc.)
│   └── Providers/              # Service providers
├── routes/
│   ├── web.php                 # All routes (152 lines) - NO API routes
│   └── console.php
├── database/
│   ├── migrations/             # 17 migration files defining schema
│   ├── factories/              # Model factories (UserFactory)
│   └── seeders/                # Database seeders (AdminSeeder, UserSeeder)
├── resources/
│   ├── views/                  # 40+ Blade templates (admin/, teacher/, student/, auth/, quizzes/, games/, forums/)
│   ├── css/app.css             # Tailwind directives
│   └── js/                     # JavaScript bootstrap
├── config/                     # Configuration files (database, auth, filesystems, etc.)
├── tests/                      # PHPUnit tests (Feature/ and Unit/)
├── public/                     # Public assets, index.php
├── storage/                    # File storage (learning materials uploaded here)
│   └── app/public/learning-materials/  # User-uploaded files
└── bootstrap/                  # Bootstrap files
```

---

## Core Domain Models & Relationships

### User Model (Central Hub)
```php
// File: app/Models/User.php
// Role field: ENUM('admin', 'teacher', 'student')

Relationships:
- learningMaterials() → hasMany(LearningMaterial)  # Teacher-created materials
- quizzes() → hasMany(Quiz)                        # Teacher-created quizzes
- quizAttempts() → hasMany(QuizAttempt)            # Student quiz attempts
- games() → hasMany(Game)                          # Teacher-created games
- gameAttempts() → hasMany(GameAttempt)            # Student game attempts
- forums() → hasMany(Forum)                        # Teacher-created forums
- forumPosts() → hasMany(ForumPost)                # User posts in forums
- assignments() → hasMany(Assignment)              # Teacher-created assignments
- assignmentSubmissions() → hasMany(AssignmentSubmission)  # Student submissions
- calendarEvents() → hasMany(CalendarEvent)        # User calendar events
- reminders() → hasMany(Reminder)                  # User reminders

Helper Methods:
- isAdmin(): bool
- isTeacher(): bool
- isStudent(): bool

Soft Deletes: Yes
```

### Key Relationships Pattern
```
Teacher (User) creates → Quiz/Game/Forum/LearningMaterial/Assignment
Student (User) creates → QuizAttempt/GameAttempt/AssignmentSubmission
Student (User) participates in → ForumPost

Quiz → hasMany(QuizQuestion)
Quiz → hasMany(QuizAttempt)
QuizAttempt → belongsTo(Quiz), belongsTo(User as student)

Forum → hasMany(ForumPost)
ForumPost → belongsTo(Forum), belongsTo(User), belongsTo(ForumPost as parent)
ForumPost → hasMany(ForumPost as replies)  # Self-referential for threading
```

### Models with Soft Deletes
- User, Quiz, LearningMaterial, Game, Forum, ForumPost, Assignment

### Models Using JSON Columns
- Quiz: `options` (JSON array of answer choices)
- QuizAttempt: `answers` (JSON array of student answers)
- Game: `game_data` (JSON object with game-specific content)

---

## Architecture Patterns & Conventions

### Naming Conventions

| Element | Convention | Example |
|---------|-----------|----------|
| Model Class | PascalCase, singular | `Quiz`, `LearningMaterial` |
| Controller | PascalCase + "Controller" | `QuizController`, `AdminController` |
| Table Name | snake_case, plural | `quizzes`, `learning_materials` |
| Route Name | snake_case with dots | `quizzes.index`, `admin.students` |
| Method Name | camelCase | `studentPerformance`, `storeStudent` |
| View Path | snake_case with dots | `quizzes.index`, `admin.students.create` |
| Migration | snake_case with timestamp | `2024_01_01_000002_create_quizzes_table.php` |
| Blade Components | kebab-case | `@include('admin.students.form')` |

### RESTful Resource Patterns

All resource controllers follow Laravel's standard 7 methods:
```php
index()    # GET /quizzes              - List resources
create()   # GET /quizzes/create       - Show creation form
store()    # POST /quizzes             - Store new resource
show()     # GET /quizzes/{id}         - Show single resource
edit()     # GET /quizzes/{id}/edit    - Show edit form
update()   # PUT/PATCH /quizzes/{id}   - Update resource
destroy()  # DELETE /quizzes/{id}      - Delete resource
```

### Authorization Pattern

**Two-Layer Authorization:**

1. **Middleware Layer** (`RoleMiddleware`):
   ```php
   // In routes/web.php
   Route::middleware(['auth', 'role:teacher'])->group(function () {
       Route::resource('quizzes', QuizController::class);
   });
   ```

2. **Policy Layer** (Fine-grained control):
   ```php
   // In controller methods
   public function update(Request $request, Quiz $quiz) {
       $this->authorize('update', $quiz);  // Checks QuizPolicy
       // ... update logic
   }
   ```

**Policy Authorization Rules Pattern:**
```php
// app/Policies/QuizPolicy.php
public function create(User $user): bool {
    return $user->isTeacher();  // Only teachers can create
}

public function update(User $user, Quiz $quiz): bool {
    return $user->isTeacher() && $quiz->teacher_id === $user->id;  // Only owner
}

public function take(User $user, Quiz $quiz): bool {
    return $user->isStudent() && $quiz->is_published;  // Only published for students
}
```

### Controller Patterns

**Constructor Middleware** (used in some controllers):
```php
public function __construct() {
    $this->middleware(function ($request, $next) {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    });
}
```

**Role-Aware Logic** (common pattern):
```php
public function index() {
    if (auth()->user()->isTeacher()) {
        // Show teacher's own quizzes
        $quizzes = Quiz::where('teacher_id', auth()->id())->paginate(15);
    } else {
        // Show published quizzes for students
        $quizzes = Quiz::where('is_published', true)->paginate(15);
    }
    return view('quizzes.index', compact('quizzes'));
}
```

**Automatic Grading Pattern** (QuizController):
```php
// In QuizController::submit()
foreach ($quiz->questions as $question) {
    $studentAnswer = $answers[$question->id] ?? null;
    $correctAnswer = $question->correct_answer;

    if ($question->question_type === 'short_answer') {
        if (trim(strtolower($studentAnswer)) === trim(strtolower($correctAnswer))) {
            $score += $question->points;
        }
    } else {
        if ($studentAnswer === $correctAnswer) {
            $score += $question->points;
        }
    }
}
```

### Query Scopes (Custom)

**CalendarEvent Model:**
```php
public function scopeForUser($query, $userId) {
    return $query->where('user_id', $userId);
}

public function scopeInDateRange($query, $start, $end) {
    return $query->whereBetween('date', [$start, $end]);
}

public function scopeUpcoming($query) {
    return $query->where('date', '>=', now())->orderBy('date');
}
```

**Reminder Model:**
```php
public function scopeForUser($query, $userId) {
    return $query->where('user_id', $userId);
}

public function scopeIncomplete($query) {
    return $query->where('is_completed', false);
}

public function scopeUpcoming($query) {
    return $query->where('date', '>=', now())->orderBy('date');
}
```

---

## Development Workflows

### Common Tasks

#### 1. Adding a New Feature (e.g., "Announcements")

**Step 1: Create Migration**
```bash
php artisan make:migration create_announcements_table
```

In migration file:
```php
Schema::create('announcements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
    $table->string('title');
    $table->text('content');
    $table->boolean('is_published')->default(false);
    $table->timestamps();
    $table->softDeletes();
});
```

**Step 2: Create Model**
```bash
php artisan make:model Announcement
```

In `app/Models/Announcement.php`:
```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model {
    use SoftDeletes;

    protected $fillable = ['teacher_id', 'title', 'content', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
```

**Step 3: Create Controller**
```bash
php artisan make:controller AnnouncementController --resource
```

**Step 4: Create Policy**
```bash
php artisan make:policy AnnouncementPolicy --model=Announcement
```

**Step 5: Add Routes**
In `routes/web.php`:
```php
Route::middleware(['auth', 'role:teacher'])->group(function () {
    Route::resource('announcements', AnnouncementController::class);
});
```

**Step 6: Create Views**
```
resources/views/announcements/
    index.blade.php
    create.blade.php
    edit.blade.php
    show.blade.php
```

**Step 7: Run Migration**
```bash
php artisan migrate
```

#### 2. Modifying an Existing Feature

**Step 1: Create Migration for Schema Change**
```bash
php artisan make:migration add_due_date_to_quizzes_table
```

**Step 2: Update Model**
Add new field to `$fillable` and `$casts` if needed.

**Step 3: Update Controller**
Add validation rules, update logic as needed.

**Step 4: Update Views**
Add form fields, display new data.

**Step 5: Run Migration**
```bash
php artisan migrate
```

#### 3. Adding a New Role-Based Route

```php
// In routes/web.php
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->group(function () {
    Route::get('/analytics', [TeacherController::class, 'analytics'])->name('teacher.analytics');
});
```

#### 4. File Upload Feature

**Pattern used in LearningMaterialController:**
```php
public function store(Request $request) {
    $validated = $request->validate([
        'title' => 'required|max:255',
        'file' => 'required|file|max:51200|mimes:pdf,doc,docx,ppt,pptx,mp4,avi,mov',
    ]);

    $path = $request->file('file')->store('learning-materials', 'public');

    LearningMaterial::create([
        'teacher_id' => auth()->id(),
        'title' => $validated['title'],
        'file_path' => $path,
        'file_type' => $request->file('file')->getClientOriginalExtension(),
    ]);

    return redirect()->route('learning-materials.index')
        ->with('success', 'Material uploaded successfully.');
}
```

**Key Points:**
- Files stored in `storage/app/public/learning-materials/`
- Max file size: 50MB (51200 KB)
- Supported formats: PDF, DOC, DOCX, PPT, PPTX, MP4, AVI, MOV
- Always validate file type and size
- Use `store()` method with 'public' disk
- Store relative path in database

#### 5. Creating Seeders

**Pattern:**
```php
// database/seeders/AdminSeeder.php
public function run() {
    User::create([
        'name' => 'Admin',
        'email' => 'admin@normninja.com',
        'password' => Hash::make('admin123'),
        'role' => 'admin',
        'is_active' => true,
    ]);
}
```

**Run seeder:**
```bash
php artisan db:seed --class=AdminSeeder
```

#### 6. Testing

**Run all tests:**
```bash
composer test
# OR
php artisan test
```

**Create a new test:**
```bash
php artisan make:test QuizTest           # Feature test
php artisan make:test QuizTest --unit    # Unit test
```

---

## Key Conventions & Best Practices

### Security Best Practices

1. **Always authorize before actions:**
   ```php
   public function update(Request $request, Quiz $quiz) {
       $this->authorize('update', $quiz);  // ALWAYS CHECK FIRST
       // ... update logic
   }
   ```

2. **Validate all inputs:**
   ```php
   $validated = $request->validate([
       'title' => 'required|max:255',
       'email' => 'required|email|unique:users,email,' . $user->id,
   ]);
   ```

3. **Hash passwords (never store plaintext):**
   ```php
   'password' => Hash::make($request->password),
   ```

4. **Use CSRF protection (included in forms):**
   ```blade
   <form method="POST" action="...">
       @csrf
       @method('PUT')  <!-- For PUT/PATCH/DELETE -->
   </form>
   ```

5. **Protect file uploads:**
   - Validate file types with `mimes:` rule
   - Limit file size with `max:` rule
   - Store files outside public directory (use `storage/app/public/`)
   - Never trust user-provided filenames

6. **Prevent SQL injection:**
   - Use Eloquent ORM (automatic parameter binding)
   - Use query builder with bindings
   - Never concatenate user input into SQL

7. **XSS Prevention:**
   - Blade automatically escapes: `{{ $variable }}`
   - Only use `{!! $html !!}` for trusted, sanitized content

### Database Conventions

1. **Foreign Keys Pattern:**
   ```php
   $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
   ```

2. **Soft Deletes:**
   ```php
   // In migration
   $table->softDeletes();

   // In model
   use SoftDeletes;
   ```

3. **JSON Columns:**
   ```php
   // In migration
   $table->json('options')->nullable();

   // In model
   protected $casts = [
       'options' => 'array',
   ];
   ```

4. **Boolean Defaults:**
   ```php
   $table->boolean('is_published')->default(false);
   ```

5. **Timestamps:**
   ```php
   $table->timestamps();  // Always include (created_at, updated_at)
   ```

### View Conventions

1. **Layout Structure:**
   ```blade
   @extends('layouts.app')
   @section('title', 'Page Title')
   @section('content')
       <!-- Content here -->
   @endsection
   ```

2. **Role-Based Color Coding:**
   - Admin: Blue (`bg-blue-600`, `bg-indigo-600`)
   - Teacher: Green/Teal (`bg-green-600`, `bg-teal-600`)
   - Student: Purple (`bg-purple-600`)
   - Success: Green (`bg-green-100`, text-green-800`)
   - Warning: Yellow (`bg-yellow-100`, text-yellow-800`)
   - Error: Red (`bg-red-100`, text-red-800`)

3. **Card Pattern:**
   ```blade
   <div class="bg-white rounded-lg shadow-md p-6">
       <h2 class="text-xl font-bold mb-4">Card Title</h2>
       <!-- Card content -->
   </div>
   ```

4. **Button Pattern:**
   ```blade
   <a href="..." class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg inline-block">
       <i class="fas fa-plus mr-2"></i> Action
   </a>
   ```

5. **Form Input Pattern:**
   ```blade
   <div class="mb-4">
       <label class="block text-gray-700 mb-2">Field Label</label>
       <input type="text" name="field"
              value="{{ old('field', $model->field ?? '') }}"
              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
       @error('field')
           <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
       @enderror
   </div>
   ```

6. **Empty State Pattern:**
   ```blade
   @forelse($items as $item)
       <!-- Item display -->
   @empty
       <p class="text-gray-500 text-center py-8">No items found.</p>
   @endforelse
   ```

7. **Pagination:**
   ```blade
   <div class="mt-6">
       {{ $items->links() }}
   </div>
   ```

### Route Organization

Routes are organized by role in `routes/web.php`:

```php
// Public routes
Route::get('/', ...);
Route::get('/login', ...);
Route::post('/login', ...);

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', ...)->name('admin.dashboard');
    Route::resource('students', AdminController::class);
});

// Teacher routes
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->group(function () {
    Route::get('/dashboard', ...)->name('teacher.dashboard');
});

// Student routes
Route::middleware(['auth', 'role:student'])->prefix('student')->group(function () {
    Route::get('/dashboard', ...)->name('student.dashboard');
});

// Shared authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::resource('quizzes', QuizController::class);
    // Controllers handle role-specific logic
});
```

---

## Common Pitfalls & How to Avoid Them

### 1. Authorization Issues

**WRONG:**
```php
public function update(Request $request, Quiz $quiz) {
    // Missing authorization check - anyone can update!
    $quiz->update($request->all());
}
```

**RIGHT:**
```php
public function update(Request $request, Quiz $quiz) {
    $this->authorize('update', $quiz);  // Check policy first
    $quiz->update($request->validated());
}
```

### 2. Mass Assignment Vulnerability

**WRONG:**
```php
User::create($request->all());  // Dangerous! Could set 'role' to 'admin'
```

**RIGHT:**
```php
User::create($request->validated());  // Only validated fields
// OR
User::create($request->only(['name', 'email', 'password']));  // Explicit fields
```

### 3. N+1 Query Problem

**WRONG:**
```php
$quizzes = Quiz::all();
foreach ($quizzes as $quiz) {
    echo $quiz->teacher->name;  // N+1 queries!
}
```

**RIGHT:**
```php
$quizzes = Quiz::with('teacher')->get();  // Eager loading
foreach ($quizzes as $quiz) {
    echo $quiz->teacher->name;  // Single query
}
```

### 4. Missing Validation

**WRONG:**
```php
public function store(Request $request) {
    Quiz::create($request->all());  // No validation!
}
```

**RIGHT:**
```php
public function store(Request $request) {
    $validated = $request->validate([
        'title' => 'required|max:255',
        'duration_minutes' => 'required|integer|min:1',
        'passing_score' => 'required|integer|min:0|max:100',
    ]);

    Quiz::create([
        'teacher_id' => auth()->id(),
        ...$validated,
    ]);
}
```

### 5. Hardcoded User IDs

**WRONG:**
```php
$quizzes = Quiz::where('teacher_id', 1)->get();  // Hardcoded ID!
```

**RIGHT:**
```php
$quizzes = Quiz::where('teacher_id', auth()->id())->get();  // Dynamic
```

### 6. Not Using Route Names

**WRONG:**
```blade
<a href="/quizzes/{{ $quiz->id }}/edit">Edit</a>  <!-- Brittle -->
```

**RIGHT:**
```blade
<a href="{{ route('quizzes.edit', $quiz) }}">Edit</a>  <!-- Flexible -->
```

### 7. Forgetting Soft Deletes in Queries

**WRONG:**
```php
$users = User::withTrashed()->get();  // Includes deleted users
```

**RIGHT:**
```php
$users = User::get();  // Automatically excludes soft-deleted
// OR explicitly:
$users = User::onlyTrashed()->get();  // Only deleted
```

### 8. Not Checking `is_published` Status

**WRONG:**
```php
public function show(Quiz $quiz) {
    return view('quizzes.show', compact('quiz'));  // Student can see unpublished!
}
```

**RIGHT:**
```php
public function show(Quiz $quiz) {
    if (!$quiz->is_published && !auth()->user()->isTeacher()) {
        abort(403, 'This quiz is not published yet.');
    }
    return view('quizzes.show', compact('quiz'));
}
```

### 9. Incorrect Role Checks

**WRONG:**
```php
if ($user->role == 'teacher') {  // Typo-prone, no IDE support
    // ...
}
```

**RIGHT:**
```php
if ($user->isTeacher()) {  // Type-safe, clear, reusable
    // ...
}
```

### 10. Missing CSRF Token

**WRONG:**
```blade
<form method="POST" action="...">
    <!-- Missing @csrf -->
    <button type="submit">Submit</button>
</form>
```

**RIGHT:**
```blade
<form method="POST" action="...">
    @csrf
    @method('PUT')  <!-- If needed for PUT/PATCH/DELETE -->
    <button type="submit">Submit</button>
</form>
```

---

## File Organization Reference

### Controllers Location Map
```
app/Http/Controllers/
├── AuthController.php              # login, logout, register
├── AdminController.php             # User management (students, teachers)
├── TeacherController.php           # Teacher dashboard, student performance analytics
├── StudentController.php           # Student dashboard, calendar, reminders
├── QuizController.php              # Quiz CRUD + start, take, submit, result
├── QuizQuestionController.php      # Question management within quizzes
├── LearningMaterialController.php  # File upload/download, material management
├── GameController.php              # Game CRUD + play, submit, results
├── ForumController.php             # Forum CRUD + posts (storePost, updatePost, deletePost)
├── CalendarEventController.php     # Calendar event CRUD
└── ReminderController.php          # Reminder CRUD + toggle completion
```

### Views Location Map
```
resources/views/
├── layouts/
│   └── app.blade.php               # Main layout with navigation
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── students/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   └── teachers/
│       ├── index.blade.php
│       ├── create.blade.php
│       └── edit.blade.php
├── teacher/
│   ├── dashboard.blade.php
│   ├── student-performance.blade.php
│   └── student-detail.blade.php
├── student/
│   └── dashboard.blade.php
├── quizzes/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── show.blade.php
│   ├── take.blade.php
│   ├── result.blade.php
│   └── questions/
│       ├── index.blade.php
│       ├── create.blade.php
│       └── edit.blade.php
├── learning-materials/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── games/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── show.blade.php
│   └── play.blade.php
├── forums/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
└── calendar/
    └── index.blade.php
```

---

## Database Schema Quick Reference

### Core Tables

**users** (central table)
- id, name, email, password, role (admin/teacher/student)
- student_id (unique, nullable), teacher_id (unique, nullable)
- phone, address, date_of_birth, profile_picture
- is_active (boolean), email_verified_at
- timestamps, softDeletes

**quizzes**
- id, teacher_id (FK → users)
- title, description, subject
- duration_minutes, passing_score
- is_published, available_from, available_until
- timestamps, softDeletes

**quiz_questions**
- id, quiz_id (FK → quizzes)
- question_type (multiple_choice, true_false, short_answer)
- question_text, options (JSON), correct_answer, points, order
- timestamps

**quiz_attempts**
- id, quiz_id (FK), student_id (FK → users)
- answers (JSON), score, total_points, percentage
- is_completed, started_at, completed_at
- timestamps

**learning_materials**
- id, teacher_id (FK)
- title, description, file_path, file_type
- subject, grade_level, is_published
- timestamps, softDeletes

**games**
- id, teacher_id (FK)
- title, description, game_type (flashcard, matching, gamified_quiz)
- subject, game_data (JSON), is_published
- timestamps, softDeletes

**game_attempts**
- id, game_id (FK), student_id (FK)
- score, time_spent_seconds, is_completed
- timestamps

**forums**
- id, teacher_id (FK)
- title, description, subject, is_active
- timestamps, softDeletes

**forum_posts**
- id, forum_id (FK), user_id (FK), parent_id (FK, self-referential)
- content (TEXT)
- timestamps, softDeletes

**assignments**
- id, teacher_id (FK)
- title, description, subject, due_date
- total_points, is_published
- timestamps, softDeletes

**assignment_submissions**
- id, assignment_id (FK), student_id (FK)
- content, file_path, score, feedback
- status (submitted, graded, late, missing)
- submitted_at
- timestamps

**calendar_events**
- id, user_id (FK)
- title, date, description, color
- timestamps

**reminders**
- id, user_id (FK)
- text, date, is_completed
- timestamps

---

## Development Commands Reference

### Setup & Installation
```bash
composer install                    # Install PHP dependencies
cp .env.example .env               # Create environment file
php artisan key:generate           # Generate app key
php artisan migrate                # Run migrations
php artisan storage:link           # Create storage symlink
npm install                        # Install frontend dependencies
npm run build                      # Build frontend assets
```

### Development
```bash
composer dev                       # Start all dev servers (Laravel, queue, logs, Vite)
php artisan serve                  # Start Laravel dev server only
npm run dev                        # Start Vite dev server only
php artisan queue:listen           # Start queue worker
```

### Database
```bash
php artisan migrate                # Run pending migrations
php artisan migrate:fresh          # Drop all tables and re-run migrations
php artisan migrate:fresh --seed   # Fresh migration + seeders
php artisan db:seed                # Run all seeders
php artisan db:seed --class=AdminSeeder  # Run specific seeder
php artisan tinker                 # Interactive console
```

### Code Generation
```bash
php artisan make:model ModelName -m              # Model + migration
php artisan make:controller ControllerName --resource  # Resource controller
php artisan make:migration create_table_name     # Create migration
php artisan make:policy PolicyName --model=Model # Create policy
php artisan make:seeder SeederName              # Create seeder
php artisan make:test TestName                  # Feature test
php artisan make:test TestName --unit           # Unit test
```

### Cache & Optimization
```bash
php artisan config:clear           # Clear config cache
php artisan cache:clear            # Clear application cache
php artisan route:clear            # Clear route cache
php artisan view:clear             # Clear compiled views
php artisan optimize               # Optimize for production
php artisan config:cache           # Cache config (production)
php artisan route:cache            # Cache routes (production)
php artisan view:cache             # Cache views (production)
```

### Testing
```bash
composer test                      # Run all tests
php artisan test                   # Run all tests
php artisan test --filter QuizTest # Run specific test
```

---

## Testing Guidelines

### Test Structure
```php
// tests/Feature/QuizTest.php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuizTest extends TestCase
{
    use RefreshDatabase;  // Reset database after each test

    public function test_teacher_can_create_quiz()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);

        $response = $this->actingAs($teacher)->post('/quizzes', [
            'title' => 'Test Quiz',
            'description' => 'Test description',
            'subject' => 'Math',
            'duration_minutes' => 60,
            'passing_score' => 70,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quizzes', [
            'title' => 'Test Quiz',
            'teacher_id' => $teacher->id,
        ]);
    }

    public function test_student_cannot_create_quiz()
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->post('/quizzes', [
            'title' => 'Test Quiz',
        ]);

        $response->assertForbidden();
    }
}
```

### Test Database Configuration
- Uses SQLite in-memory database (`:memory:`)
- Automatically configured in `phpunit.xml`
- Use `RefreshDatabase` trait to reset between tests
- Create test data with factories or manually

---

## Environment Configuration

### Key .env Variables

```env
# Application
APP_NAME=NormNinja
APP_ENV=local|production
APP_DEBUG=true|false
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=normninja
DB_USERNAME=root
DB_PASSWORD=

# Session & Cache
SESSION_DRIVER=file|database|redis
CACHE_DRIVER=file|redis

# Queue
QUEUE_CONNECTION=sync|database|redis

# File System
FILESYSTEM_DISK=public

# Mail (if needed)
MAIL_MAILER=smtp|log|array
```

### Storage Configuration
- **Disk:** `public` (defined in `config/filesystems.php`)
- **Path:** `storage/app/public/`
- **Public access:** `public/storage/` (via symlink)
- **Create symlink:** `php artisan storage:link`

---

## Important File Paths

### Configuration Files
- `config/app.php` - Application settings
- `config/database.php` - Database connections
- `config/auth.php` - Authentication settings
- `config/filesystems.php` - File storage configuration
- `config/session.php` - Session configuration

### Key Application Files
- `routes/web.php` - All web routes (152 lines)
- `app/Http/Kernel.php` - Middleware definitions
- `app/Providers/AppServiceProvider.php` - Service provider
- `bootstrap/app.php` - Application bootstrap

### Entry Points
- `public/index.php` - Web entry point
- `artisan` - CLI entry point

---

## Authentication Flow

### Login Process
1. User visits `/login` (GET)
2. `AuthController::showLoginForm()` displays login view
3. User submits form to `/login` (POST)
4. `AuthController::login()` validates credentials
5. `Auth::attempt($credentials)` checks email/password
6. Session regenerated for security
7. Redirect based on role:
   - Admin → `/admin/dashboard`
   - Teacher → `/teacher/dashboard`
   - Student → `/student/dashboard`

### Registration Process
1. User visits `/register` (GET)
2. `AuthController::showRegisterForm()` displays registration view
3. User selects role (teacher or student only, NOT admin)
4. User submits form to `/register` (POST)
5. `AuthController::register()` validates input
6. Password hashed with `Hash::make()`
7. User created and logged in automatically
8. Redirect to appropriate dashboard

### Logout Process
1. User submits to `/logout` (POST)
2. `AuthController::logout()` invalidates session
3. CSRF token regenerated
4. Redirect to `/login`

---

## Role-Based Access Summary

### Admin Capabilities
- Manage students (CRUD)
- Manage teachers (CRUD)
- View system statistics
- Access: `/admin/*` routes

### Teacher Capabilities
- Create/manage learning materials
- Create/manage quizzes (with questions)
- Create/manage games
- Create/manage forums
- View student performance analytics
- Identify students needing support
- Access: `/teacher/*` routes + shared content routes

### Student Capabilities
- Access published learning materials
- Take published quizzes
- Play published games
- Participate in active forums
- View personal performance
- Manage calendar events
- Manage reminders
- Access: `/student/*` routes + shared content routes

---

## Performance Considerations

### Pagination
- Default: 15-20 items per page
- Used for: students, teachers, quizzes, materials, games, forums
- Example: `Quiz::paginate(15)`

### Eager Loading
- Always use `with()` for relationships to avoid N+1 queries
- Example: `Quiz::with('teacher', 'questions')->get()`

### Caching
- Production: Use Redis for session and cache
- Development: Use file driver
- Clear cache during development: `php artisan cache:clear`

### Database Indexing
- Foreign keys automatically indexed
- Consider adding indexes for frequently queried columns (subject, is_published, etc.)

---

## Deployment Checklist

### Pre-Deployment
- [ ] Run tests: `composer test`
- [ ] Update `.env` for production
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Configure database credentials
- [ ] Set secure `APP_KEY`

### Deployment Steps
- [ ] `composer install --optimize-autoloader --no-dev`
- [ ] `npm run build`
- [ ] `php artisan migrate --force`
- [ ] `php artisan storage:link`
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] Set file permissions: `chmod -R 775 storage bootstrap/cache`

### Post-Deployment
- [ ] Create admin user via tinker
- [ ] Test login for all roles
- [ ] Verify file uploads work
- [ ] Check error logs: `storage/logs/laravel.log`

---

## Troubleshooting Common Issues

### Issue: 403 Forbidden on route
**Cause:** Missing authorization or role check
**Solution:**
1. Check route middleware in `routes/web.php`
2. Check policy authorization in controller
3. Verify user has correct role

### Issue: File upload not working
**Cause:** Storage symlink missing
**Solution:** `php artisan storage:link`

### Issue: 500 Error
**Solution:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Issue: Class not found
**Solution:** `composer dump-autoload`

### Issue: Changes not reflecting
**Solution:**
```bash
php artisan config:clear  # Clear config cache
php artisan view:clear    # Clear view cache
npm run build             # Rebuild frontend
```

---

## Additional Resources

- **Laravel Documentation:** https://laravel.com/docs
- **Tailwind CSS Documentation:** https://tailwindcss.com/docs
- **Font Awesome Icons:** https://fontawesome.com/icons
- **PHPUnit Documentation:** https://phpunit.de/documentation.html

---

## Quick Reference: Common Code Snippets

### Get Current User
```php
$user = auth()->user();
$userId = auth()->id();
```

### Check User Role
```php
if (auth()->user()->isAdmin()) { ... }
if (auth()->user()->isTeacher()) { ... }
if (auth()->user()->isStudent()) { ... }
```

### Redirect with Success Message
```php
return redirect()->route('quizzes.index')
    ->with('success', 'Quiz created successfully.');
```

### Display Flash Messages in View
```blade
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
@endif
```

### Authorize Action
```php
$this->authorize('update', $quiz);
```

### Validate Request
```php
$validated = $request->validate([
    'title' => 'required|max:255',
    'email' => 'required|email|unique:users',
    'file' => 'required|file|max:51200|mimes:pdf,doc,docx',
]);
```

### Create Record
```php
Quiz::create([
    'teacher_id' => auth()->id(),
    'title' => $validated['title'],
    'is_published' => false,
]);
```

### Update Record
```php
$quiz->update($validated);
```

### Soft Delete
```php
$quiz->delete();  // Soft delete (can be restored)
```

### Permanent Delete
```php
$quiz->forceDelete();  // Permanent delete
```

### Restore Soft Deleted
```php
$quiz->restore();
```

### Query Soft Deleted
```php
Quiz::withTrashed()->get();      // Include soft deleted
Quiz::onlyTrashed()->get();      // Only soft deleted
Quiz::get();                     // Exclude soft deleted (default)
```

---

## Final Notes for AI Assistants

1. **Always prioritize security:** Check authorization before actions, validate all inputs, never trust user data.

2. **Follow existing patterns:** This codebase has established conventions. Match the existing style and structure.

3. **Use Eloquent ORM:** Avoid raw SQL. Use Eloquent models and query builder.

4. **Test your changes:** Run `composer test` before committing.

5. **Check related files:** When modifying a feature, update model, controller, routes, views, and policies as needed.

6. **Use meaningful names:** Follow Laravel naming conventions for consistency.

7. **Document complex logic:** Add comments for non-obvious business logic.

8. **Keep it simple:** Don't over-engineer. Follow KISS principle.

9. **Refer to existing examples:** When unsure, look at how similar features are implemented (e.g., Quiz → Game pattern).

10. **Ask for clarification:** If requirements are unclear, ask before implementing.

---

**This document is maintained to help AI assistants understand and work effectively with the NormNinja LMS codebase. Last updated: 2025-11-30**
