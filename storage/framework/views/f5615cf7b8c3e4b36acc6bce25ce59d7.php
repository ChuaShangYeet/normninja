<?php $__env->startSection('title', $game->title); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <a href="<?php echo e(route('games.index')); ?>" class="text-indigo-600 hover:text-indigo-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Games
            </a>
            
            <?php if(auth()->user()->isTeacher() && auth()->id() === $game->teacher_id): ?>
            <div class="flex gap-2">
                <a href="<?php echo e(route('games.edit', $game)); ?>" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="<?php echo e(route('games.statistics', $game)); ?>" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
                    <i class="fas fa-chart-bar mr-2"></i>Statistics
                </a>
                <form action="<?php echo e(route('games.destroy', $game)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this game?');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Game Info Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <!-- Header with Game Type -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold uppercase tracking-wide">
                            <?php switch($game->game_type):
                                case ('flashcard'): ?>
                                    <i class="fas fa-layer-group mr-2"></i>Flashcards
                                    <?php break; ?>
                                <?php case ('matching'): ?>
                                    <i class="fas fa-puzzle-piece mr-2"></i>Matching Game
                                    <?php break; ?>
                                <?php case ('gamified_quiz'): ?>
                                    <i class="fas fa-gamepad mr-2"></i>Gamified Quiz
                                    <?php break; ?>
                            <?php endswitch; ?>
                        </span>
                        <?php if(!$game->is_published): ?>
                        <span class="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold">
                            DRAFT
                        </span>
                        <?php else: ?>
                        <span class="bg-green-400 text-green-900 px-3 py-1 rounded-full text-xs font-bold">
                            PUBLISHED
                        </span>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-3xl font-bold"><?php echo e($game->title); ?></h1>
                    <?php if($game->subject): ?>
                    <p class="text-indigo-100 mt-2">
                        <i class="fas fa-book mr-2"></i><?php echo e($game->subject); ?>

                    </p>
                    <?php endif; ?>
                </div>

                <!-- Game Details -->
                <div class="p-6">
                    <?php if($game->description): ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                        <p class="text-gray-600"><?php echo e($game->description); ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Created By</p>
                            <p class="font-semibold text-gray-800"><?php echo e($game->teacher->name); ?></p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Created</p>
                            <p class="font-semibold text-gray-800"><?php echo e($game->created_at->format('M d, Y')); ?></p>
                        </div>
                    </div>

                    <!-- Game Content Preview -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Content Preview</h3>

                        <?php if($game->game_type === 'flashcard'): ?>
                            <div class="space-y-3">
                                <p class="text-gray-600 mb-3">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <?php echo e(count($game->game_data['flashcards'] ?? [])); ?> flashcards
                                </p>
                                <?php $__currentLoopData = ($game->game_data['flashcards'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($index < 3): ?>
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 mb-1">FRONT</p>
                                                <p class="text-gray-800"><?php echo e($card['front']); ?></p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 mb-1">BACK</p>
                                                <p class="text-gray-800"><?php echo e($card['back']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($game->game_data['flashcards'] ?? []) > 3): ?>
                                <p class="text-sm text-gray-500 italic">
                                    + <?php echo e(count($game->game_data['flashcards']) - 3); ?> more flashcards
                                </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if($game->game_type === 'matching'): ?>
                            <div class="space-y-3">
                                <p class="text-gray-600 mb-3">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <?php echo e(count($game->game_data['pairs'] ?? [])); ?> matching pairs
                                </p>
                                <?php $__currentLoopData = ($game->game_data['pairs'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pair): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($index < 3): ?>
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 mb-1">TERM</p>
                                                <p class="text-gray-800"><?php echo e($pair['term']); ?></p>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-500 mb-1">DEFINITION</p>
                                                <p class="text-gray-800"><?php echo e($pair['definition']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($game->game_data['pairs'] ?? []) > 3): ?>
                                <p class="text-sm text-gray-500 italic">
                                    + <?php echo e(count($game->game_data['pairs']) - 3); ?> more pairs
                                </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if($game->game_type === 'gamified_quiz'): ?>
                            <div class="space-y-3">
                                <p class="text-gray-600 mb-3">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <?php echo e(count($game->game_data['questions'] ?? [])); ?> questions
                                </p>
                                <?php $__currentLoopData = ($game->game_data['questions'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($index < 3): ?>
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <p class="font-semibold text-gray-800 mb-2"><?php echo e($index + 1); ?>. <?php echo e($question['question']); ?></p>
                                        <div class="space-y-1 ml-4">
                                            <?php $__currentLoopData = $question['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optIndex => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="flex items-center">
                                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold mr-2
                                                    <?php echo e($question['correct'] == $optIndex ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'); ?>">
                                                    <?php echo e(chr(65 + $optIndex)); ?>

                                                </span>
                                                <span class="text-gray-700"><?php echo e($option); ?></span>
                                                <?php if($question['correct'] == $optIndex): ?>
                                                <i class="fas fa-check text-green-600 ml-2"></i>
                                                <?php endif; ?>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2">
                                            <i class="fas fa-star text-yellow-500"></i> <?php echo e($question['points']); ?> points
                                        </p>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if(count($game->game_data['questions'] ?? []) > 3): ?>
                                <p class="text-sm text-gray-500 italic">
                                    + <?php echo e(count($game->game_data['questions']) - 3); ?> more questions
                                </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Play Card (Students) -->
            <?php if(auth()->user()->isStudent() && $game->is_published): ?>
            <div class="bg-gradient-to-br from-green-500 to-teal-600 rounded-lg shadow-lg p-6 text-white mb-6">
                <h3 class="text-xl font-bold mb-4">Ready to Play?</h3>
                <p class="mb-4 text-green-100">
                    Test your knowledge and have fun learning!
                </p>
                <a href="<?php echo e(route('games.play', $game)); ?>" 
                   class="block w-full bg-white text-green-600 text-center px-6 py-3 rounded-lg font-bold hover:bg-green-50 transition duration-200">
                    <i class="fas fa-play mr-2"></i>Start Game
                </a>
            </div>
            <?php endif; ?>

            <!-- Stats Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Statistics</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-gray-600">Total Attempts</span>
                            <span class="font-bold text-gray-800"><?php echo e($game->attempts()->count()); ?></span>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-gray-600">Completed</span>
                            <span class="font-bold text-green-600"><?php echo e($game->attempts()->where('is_completed', true)->count()); ?></span>
                        </div>
                    </div>
                    <?php if($game->attempts()->where('is_completed', true)->count() > 0): ?>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-gray-600">Average Score</span>
                            <span class="font-bold text-indigo-600"><?php echo e(round($game->averageScore(), 1)); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Your Attempts (Students) -->
            <?php if(auth()->user()->isStudent() && $userAttempt): ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Your Progress</h3>
                <div class="space-y-3">
                    <?php
                        $attempts = $game->studentAttempts(auth()->id())->latest()->take(5)->get();
                    ?>
                    <?php $__currentLoopData = $attempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-semibold text-gray-800">
                                <?php if($attempt->is_completed): ?>
                                    Score: <?php echo e($attempt->score); ?>

                                <?php else: ?>
                                    <span class="text-yellow-600">In Progress</span>
                                <?php endif; ?>
                            </p>
                            <p class="text-xs text-gray-500"><?php echo e($attempt->created_at->diffForHumans()); ?></p>
                        </div>
                        <?php if($attempt->is_completed): ?>
                        <a href="<?php echo e(route('games.results', $attempt)); ?>" 
                           class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                            View <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\normninja\resources\views/games/show.blade.php ENDPATH**/ ?>