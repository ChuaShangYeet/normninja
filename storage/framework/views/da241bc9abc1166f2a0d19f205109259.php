<?php $__env->startSection('title', $quiz->title); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="<?php echo e(route('quizzes.index')); ?>" class="text-green-600 hover:text-green-800 mr-4">
                <i class="fas fa-arrow-left"></i> Back to Quizzes
            </a>
            <?php if(auth()->user()->isTeacher() && auth()->id() === $quiz->teacher_id): ?>
        <div class="flex gap-2">
            <a href="<?php echo e(route('quizzes.questions.index', $quiz)); ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
                <i class="fas fa-list mr-2"></i>Manage Questions
            </a>
            <a href="<?php echo e(route('quizzes.edit', $quiz)); ?>" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
                <i class="fas fa-edit mr-2"></i>Edit Quiz
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <!-- Quiz Info Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-500 to-teal-600 p-6 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold uppercase tracking-wide">
                        <i class="fas fa-clipboard-list mr-2"></i>Quiz
                    </span>
                    <?php if(!$quiz->is_published): ?>
                    <span class="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold">
                        DRAFT
                    </span>
                    <?php else: ?>
                    <span class="bg-white text-green-600 px-3 py-1 rounded-full text-xs font-bold">
                        PUBLISHED
                    </span>
                    <?php endif; ?>
                </div>
                <h1 class="text-3xl font-bold"><?php echo e($quiz->title); ?></h1>
                <?php if($quiz->subject): ?>
                <p class="text-green-100 mt-2">
                    <i class="fas fa-book mr-2"></i><?php echo e($quiz->subject); ?>

                </p>
                <?php endif; ?>
            </div>

            <!-- Details -->
            <div class="p-6">
                <?php if($quiz->description): ?>
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                    <p class="text-gray-600"><?php echo e($quiz->description); ?></p>
                </div>
                <?php endif; ?>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">Created By</p>
                        <p class="font-semibold text-gray-800"><?php echo e($quiz->teacher->name); ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 mb-1">Created</p>
                        <p class="font-semibold text-gray-800"><?php echo e($quiz->created_at->format('M d, Y')); ?></p>
                    </div>
                </div>

                <!-- Availability -->
                <?php if($quiz->available_from || $quiz->available_until): ?>
                <div class="border-t pt-4 mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Availability</h3>
                    <div class="space-y-2">
                        <?php if($quiz->available_from): ?>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Available from: <?php echo e($quiz->available_from->format('M d, Y h:i A')); ?>

                        </p>
                        <?php endif; ?>
                        <?php if($quiz->available_until): ?>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-calendar-times mr-2"></i>
                            Available until: <?php echo e($quiz->available_until->format('M d, Y h:i A')); ?>

                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Student Attempts History -->
        <?php if(auth()->user()->isStudent() && $userAttempts && $userAttempts->count() > 0): ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-history mr-2 text-green-600"></i>Your Attempts
            </h2>
            <div class="space-y-3">
                <?php $__currentLoopData = $userAttempts->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border rounded-lg p-4 <?php echo e($attempt->passed ? 'bg-green-50 border-green-200' : 'bg-gray-50'); ?>">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-800">
                                <?php echo e($attempt->created_at->format('M d, Y h:i A')); ?>

                            </p>
                            <?php if($attempt->is_completed): ?>
                            <p class="text-2xl font-bold <?php echo e($attempt->passed ? 'text-green-600' : 'text-orange-600'); ?>">
                                <?php echo e($attempt->percentage); ?>%
                            </p>
                            <?php else: ?>
                            <p class="text-sm text-yellow-600">In Progress</p>
                            <?php endif; ?>
                        </div>
                        <?php if($attempt->is_completed): ?>
                        <div class="text-right">
                            <?php if($attempt->passed): ?>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-check mr-1"></i>PASSED
                            </span>
                            <?php else: ?>
                            <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-xs font-bold">
                                NEEDS IMPROVEMENT
                            </span>
                            <?php endif; ?>
                            <a href="<?php echo e(route('quizzes.result', [$quiz, $attempt])); ?>" 
                               class="block mt-2 text-green-600 hover:text-green-800 text-sm font-semibold">
                                View Results <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <!-- Take Quiz Card (Students) -->
        <?php if(auth()->user()->isStudent() && $quiz->is_published): ?>
        <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-lg p-6 text-white mb-6">
            <h3 class="text-xl font-bold mb-4">Ready to Start?</h3>
            <div class="space-y-3 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-question-circle w-6"></i>
                    <span class="ml-2"><?php echo e($quiz->questions()->count()); ?> Questions</span>
                </div>
                <?php if($quiz->duration_minutes): ?>
                <div class="flex items-center">
                    <i class="fas fa-clock w-6"></i>
                    <span class="ml-2"><?php echo e($quiz->duration_minutes); ?> Minutes</span>
                </div>
                <?php endif; ?>
                <div class="flex items-center">
                    <i class="fas fa-star w-6"></i>
                    <span class="ml-2"><?php echo e($quiz->total_points); ?> Points</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-trophy w-6"></i>
                    <span class="ml-2"><?php echo e($quiz->passing_score); ?>% to Pass</span>
                </div>
            </div>
            <a href="<?php echo e(route('quizzes.start', $quiz)); ?>" 
               class="block w-full bg-white text-green-600 text-center px-6 py-3 rounded-lg font-bold hover:bg-green-50 transition duration-200">
                <i class="fas fa-play mr-2"></i>Start Quiz
            </a>
        </div>
        <?php endif; ?>

        <!-- Quiz Stats -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Quiz Information</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-600">Questions</span>
                        <span class="font-bold text-gray-800"><?php echo e($quiz->questions()->count()); ?></span>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-600">Total Points</span>
                        <span class="font-bold text-gray-800"><?php echo e($quiz->total_points); ?></span>
                    </div>
                </div>
                <?php if($quiz->duration_minutes): ?>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-600">Time Limit</span>
                        <span class="font-bold text-gray-800"><?php echo e($quiz->duration_minutes); ?> min</span>
                    </div>
                </div>
                <?php endif; ?>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-600">Passing Score</span>
                        <span class="font-bold text-green-600"><?php echo e($quiz->passing_score); ?>%</span>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-600">Total Attempts</span>
                        <span class="font-bold text-gray-800"><?php echo e($quiz->attempts()->count()); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Score (Students) -->
        <?php if(auth()->user()->isStudent() && $userAttempts && $userAttempts->where('is_completed', true)->count() > 0): ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Best Score</h3>
            <?php
                $bestAttempt = $userAttempts->where('is_completed', true)->sortByDesc('score')->first();
            ?>
            <div class="text-center">
                <div class="text-5xl font-bold text-green-600 mb-2"><?php echo e($bestAttempt->percentage); ?>%</div>
                <p class="text-gray-600"><?php echo e($bestAttempt->score); ?> / <?php echo e($bestAttempt->total_points); ?> points</p>
                <p class="text-sm text-gray-500 mt-2"><?php echo e($bestAttempt->created_at->diffForHumans()); ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\normninja\resources\views/quizzes/show.blade.php ENDPATH**/ ?>