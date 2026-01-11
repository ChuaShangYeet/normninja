<?php

namespace App\Http\Controllers;

use App\Models\LearningMaterial;
use App\Models\Quiz;
use App\Models\Game;
use App\Models\Forum;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isStudent()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    public function calendarStore(Request $request)
    {
        CalendarEvent::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'date' => $request->date,
            
        ]);
        return back();
    }

    public function calendarUpdate(Request $request, CalendarEvent $event)
    {
        $event->update($request->only(['title', 'date']));
        return back();
    }

    public function calendarDelete(CalendarEvent $event)
    {
        $event->delete();
        return back();
    }

    public function dashboard(Request $request)
    {
        $student = auth()->user();

        // Get sorting parameters
        $quizSort = $request->get('quiz_sort', 'date_newest');
        $gameSort = $request->get('game_sort', 'date_newest');

        // Get recent quiz attempts with sorting
        $quizQuery = $student->quizAttempts()
            ->whereHas('quiz')
            ->with('quiz')
            ->where('is_completed', true);

        // Apply quiz sorting
        switch ($quizSort) {
            case 'alphabet_az':
                $quizQuery->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
                    ->orderBy('quizzes.title', 'asc')
                    ->select('quiz_attempts.*');
                break;
            case 'alphabet_za':
                $quizQuery->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
                    ->orderBy('quizzes.title', 'desc')
                    ->select('quiz_attempts.*');
                break;
            case 'date_oldest':
                $quizQuery->orderBy('completed_at', 'asc');
                break;
            case 'time_oldest':
                $quizQuery->orderBy('completed_at', 'asc');
                break;
            case 'time_newest':
                $quizQuery->orderBy('completed_at', 'desc');
                break;
            case 'date_newest':
            default:
                $quizQuery->orderBy('completed_at', 'desc');
                break;
        }

         // All quiz attempts by the student
        $allQuizAttempts = $student->quizAttempts;
        $completedQuizzesCount = $allQuizAttempts->where('passed', true)->count();

        $recentQuizAttempts = $quizQuery->take(5)->get();

        // Get recent game attempts with sorting
        $gameQuery = $student->gameAttempts()
            ->with('game');

        // Apply game sorting
        switch ($gameSort) {
            case 'alphabet_az':
                $gameQuery->join('games', 'game_attempts.game_id', '=', 'games.id')
                    ->orderBy('games.title', 'asc')
                    ->select('game_attempts.*');
                break;
            case 'alphabet_za':
                $gameQuery->join('games', 'game_attempts.game_id', '=', 'games.id')
                    ->orderBy('games.title', 'desc')
                    ->select('game_attempts.*');
                break;
            case 'date_oldest':
                $gameQuery->orderBy('created_at', 'asc');
                break;
            case 'date_newest':
                $gameQuery->orderBy('created_at', 'desc');
                break;
            case 'time_oldest':
                $gameQuery->orderBy('time_spent_seconds', 'asc');
                break;
            case 'time_newest':
                $gameQuery->orderBy('time_spent_seconds', 'desc');
                break;
            default:
                $gameQuery->orderBy('created_at', 'desc');
                break;
        }

        $recentGameAttempts = $gameQuery->take(5)->get();

        // Calculate average quiz score manually
        $completedAttempts = $student->quizAttempts()
            ->where('is_completed', true)
            ->get();
        
        $averageScore = 0;
        if ($completedAttempts->count() > 0) {
            $totalPercentage = 0;
            foreach ($completedAttempts as $attempt) {
                if ($attempt->total_points > 0) {
                    $totalPercentage += ($attempt->score / $attempt->total_points) * 100;
                }
            }
            $averageScore = round($totalPercentage / $completedAttempts->count(), 2);
        }

        // Statistics
        $stats = [
            'completed_quizzes' => $completedQuizzesCount,
            'course_progress' => $this->calculateCourseProgress($student),
            'games_played' => $uniqueGamesPlayed = $student->gameAttempts()
                ->distinct('game_id')
                ->count(),
            'materials_available' => LearningMaterial::where('is_published', true)->count(),
            'active_forums' => Forum::where('is_active', true)->count(),
            'calendarEvents' => CalendarEvent::where('user_id', $student->id)->get(),
        ];

        // Available content
        $availableMaterials = LearningMaterial::where('is_published', true)->latest()->take(5)->get();
        $availableQuizzes = Quiz::where('is_published', true)->latest()->take(5)->get();
        $availableGames = Game::where('is_published', true)->latest()->take(5)->get();
        $calendarEvents = CalendarEvent::where('user_id', $student->id)->get();

        return view('student.dashboard', compact(
            'stats',
            'recentQuizAttempts',
            'completedQuizzesCount',
            'recentGameAttempts',
            'availableMaterials',
            'availableQuizzes',
            'availableGames',
            'calendarEvents',
            'quizSort',
            'gameSort'
        ));
    }

    public function showProfile()
    {
        $user = auth()->user();
        return view('student.profile', compact('user'));
    }

    public function editProfile()
    {
        $user = auth()->user();
        return view('student.profile-edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [];

        // Update phone if provided
        if ($request->filled('phone')) {
            $data['phone'] = $request->phone;
        }

        // Update address if provided
        if ($request->filled('address')) {
            $data['address'] = $request->address;
        }

        // Update the user with allowed fields
        if (!empty($data)) {
            $user->update($data);
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('student.profile')->with('success', 'Profile updated successfully.');
    }

    // Add this method to calculate progress:
    <?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isTeacher()) {
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $teacher = auth()->user();
        
        $stats = [
            'total_materials' => $teacher->learningMaterials()->count(),
            'total_quizzes' => $teacher->quizzes()->count(),
            'total_games' => $teacher->games()->count(),
            'total_forums' => $teacher->forums()->count(),
            'total_students' => User::where('role', 'student')->count(),
        ];

        // Get recent activities
        $recentQuizAttempts = QuizAttempt::whereHas('quiz', function($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->with(['student', 'quiz'])->latest()->take(5)->get();

        return view('teacher.dashboard', compact('stats', 'recentQuizAttempts'));
    }

    public function studentPerformance()
    {
        $teacher = auth()->user();
        $students = User::where('role', 'student')->get();

        $performanceData = [];

        foreach ($students as $student) {
            // Quiz performance
            $quizAttempts = QuizAttempt::where('student_id', $student->id)
                ->whereHas('quiz', function($query) use ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                })
                ->where('is_completed', true)
                ->get();

            // Keep only the best attempt per quiz
            $bestQuizAttempts = $quizAttempts->groupBy('quiz_id')->map(fn($attempts) =>
                $attempts->sortByDesc(fn($a) => $a->score)->first()
            )->values();

            $completedQuizzes = $bestQuizAttempts->count();
            $totalQuizzes = $teacher->quizzes()->count();

            // Average quiz score
            $avgQuizScore = 0;
            if ($quizAttempts->count() > 0) {
                $totalPercentage = 0;
                foreach ($quizAttempts as $attempt) {
                    if ($attempt->total_points > 0) {
                        $totalPercentage += ($attempt->score / $attempt->total_points) * 100;
                    }
                }
                $avgQuizScore = $totalPercentage / $quizAttempts->count();
            }

            // Quiz completion rate
            $quizCompletionRate = $totalQuizzes > 0 ? ($completedQuizzes / $totalQuizzes) * 100 : 0;

            // Game performance
            $gamesPlayed = $student->gameAttempts()
            ->whereHas('game', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)
                  ->where('is_published', true);
            })
            ->get() // get all attempts first
            ->pluck('game_id') // get game IDs
            ->unique() // keep only unique games
            ->count(); // count unique games

            $totalGames = $teacher->games()->where('is_published', true)->count();
            $gamesCompletionRate = $totalGames > 0 ? ($gamesPlayed / $totalGames) * 100 : 0;

            // Course progress (optional, include games too)
            $courseProgress = ($totalQuizzes + $totalGames) > 0 
                ? round(($completedQuizzes + $gamesPlayed) / ($totalQuizzes + $totalGames) * 100) 
                : 0;

            // Check for declining quiz performance
            $decliningPerformance = false;
            if ($quizAttempts->count() >= 3) {
                $recentAttempts = $quizAttempts->sortByDesc('created_at')->take(3);
                $earlierAttempts = $quizAttempts->sortBy('created_at')->take(3);

                $recentAvg = $earlierAvg = 0;

                foreach ($recentAttempts as $attempt) {
                    if ($attempt->total_points > 0) {
                        $recentAvg += ($attempt->score / $attempt->total_points) * 100;
                    }
                }
                $recentAvg = $recentAvg / 3;

                foreach ($earlierAttempts as $attempt) {
                    if ($attempt->total_points > 0) {
                        $earlierAvg += ($attempt->score / $attempt->total_points) * 100;
                    }
                }
                $earlierAvg = $earlierAvg / 3;

                if ($recentAvg < $earlierAvg - 10) {
                    $decliningPerformance = true;
                }
            }

            // Determine if student needs support
            $needsSupport = false;
            $supportReasons = [];

            // Low quiz average
            if ($avgQuizScore < 60 && $completedQuizzes > 0) {
                $needsSupport = true;
                $supportReasons[] = "Low quiz average (" . round($avgQuizScore, 2) . "%)";
            }

            // Low quiz completion rate
            if ($totalQuizzes > 0 && $quizCompletionRate < 50) {
                $needsSupport = true;
                $supportReasons[] = "Low quiz completion (" . round($quizCompletionRate, 2) . "%)";
            }

            // Low game completion rate
            if ($totalGames > 0 && $gamesCompletionRate < 50) {
                $needsSupport = true;
                $supportReasons[] = "Low game completion (" . round($gamesCompletionRate, 2) . "%)";
            }

            // No quiz attempts
            if ($totalQuizzes > 0 && $completedQuizzes == 0) {
                $needsSupport = true;
                $supportReasons[] = "No quiz attempts yet";
            }

            // Declining performance
            if ($decliningPerformance) {
                $needsSupport = true;
                $supportReasons[] = "Performance declining over time";
            }

            $performanceData[] = [
                'student' => $student,
                'avg_quiz_score' => round($avgQuizScore, 2),
                'completed_quizzes' => $completedQuizzes,
                'total_quizzes' => $totalQuizzes,
                'games_played' => $gamesPlayed,
                'total_games' => $totalGames,
                'course_progress' => $courseProgress,
                'quiz_completion_rate' => round($quizCompletionRate, 2),
                'games_completion_rate' => round($gamesCompletionRate, 2),
                'needs_support' => $needsSupport,
                'support_reasons' => $supportReasons,
            ];
        }

        return view('teacher.student-performance', compact('performanceData'));
    }

    public function studentDetail($studentId)
    {
        $teacher = auth()->user();
        $student = User::where('role', 'student')->findOrFail($studentId);

        // Quiz performance
        $quizAttempts = QuizAttempt::where('student_id', $student->id)
            ->whereHas('quiz', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->with('quiz')
            ->orderBy('created_at', 'desc')
            ->get();

        // Game attempts
        $gameAttempts = $student->gameAttempts()
            ->whereHas('game', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->with('game')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.student-detail', compact('student', 'quizAttempts', 'gameAttempts'));
    }

    public function showProfile()
    {
        $user = auth()->user();
        return view('teacher.profile', compact('user'));
    }

    public function editProfile()
    {
        $user = auth()->user();
        return view('teacher.profile-edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [];

        // Update phone if provided
        if ($request->filled('phone')) {
            $data['phone'] = $request->phone;
        }

        // Update address if provided
        if ($request->filled('address')) {
            $data['address'] = $request->address;
        }

        // Update the user with allowed fields
        if (!empty($data)) {
            $user->update($data);
        }

        // Update password if provided
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('teacher.profile')->with('success', 'Profile updated successfully.');
    }
}
}
