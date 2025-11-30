

<?php $__env->startSection('title', 'Games'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Educational Games</h1>
            <p class="text-gray-600 mt-2">
                <?php if(auth()->user()->isTeacher()): ?>
                    Manage and create interactive learning games
                <?php else: ?>
                    Play games to learn and practice
                <?php endif; ?>
            </p>
        </div>
        <?php if(auth()->user()->isTeacher()): ?>
        <a href="<?php echo e(route('games.create')); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md transition duration-200">
            <i class="fas fa-plus mr-2"></i>Create Game
        </a>
        <?php endif; ?>
    </div>

    <!-- Success Message -->
    <?php if(session('success')): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <p><?php echo e(session('success')); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Games Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $games; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $game): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
            <!-- Game Type Badge -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4">
                <div class="flex items-center justify-between">
                    <span class="text-white font-semibold text-lg">
                        <?php switch($game->game_type):
                            case ('flashcard'): ?>
                                <i class="fas fa-layer-group mr-2"></i>Flashcards
                                <?php break; ?>
                            <?php case ('matching'): ?>
                                <i class="fas fa-puzzle-piece mr-2"></i>Matching
                                <?php break; ?>
                            <?php case ('gamified_quiz'): ?>
                                <i class="fas fa-gamepad mr-2"></i>Gamified Quiz
                                <?php break; ?>
                        <?php endswitch; ?>
                    </span>
                    <?php if(!$game->is_published): ?>
                    <span class="bg-yellow-400 text-yellow-900 px-2 py-1 rounded text-xs font-semibold">
                        Draft
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Game Content -->
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo e($game->title); ?></h3>
                
                <?php if($game->subject): ?>
                <p class="text-sm text-gray-500 mb-3">
                    <i class="fas fa-book mr-1"></i><?php echo e($game->subject); ?>

                </p>
                <?php endif; ?>

                <p class="text-gray-600 mb-4 line-clamp-2">
                    <?php echo e($game->description ?? 'No description provided'); ?>

                </p>

                <!-- Game Stats -->
                <div class="border-t pt-4 mb-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Created By</p>
                            <p class="font-semibold text-gray-800"><?php echo e($game->teacher->name); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500">Attempts</p>
                            <p class="font-semibold text-gray-800"><?php echo e($game->attempts()->count()); ?></p>
                        </div>
                    </div>
                    
                    <?php if(auth()->user()->isStudent() && $game->attempts()->where('student_id', auth()->id())->exists()): ?>
                    <div class="mt-3 pt-3 border-t">
                        <p class="text-sm text-gray-500">Your Best Score</p>
                        <p class="text-lg font-bold text-green-600">
                            <?php echo e($game->attempts()->where('student_id', auth()->id())->where('is_completed', true)->max('score') ?? 'N/A'); ?>

                        </p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <?php if(auth()->user()->isTeacher()): ?>
                        <a href="<?php echo e(route('games.edit', $game)); ?>" class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-semibold transition duration-200">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <a href="<?php echo e(route('games.statistics', $game)); ?>" class="flex-1 text-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-semibold transition duration-200">
                            <i class="fas fa-chart-bar mr-1"></i>Stats
                        </a>
                        <form action="<?php echo e(route('games.destroy', $game)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this game?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-semibold transition duration-200">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo e(route('games.show', $game)); ?>" class="flex-1 text-center text-blue-600 hover:text-blue-800 px-4 py-2 rounded text-sm font-semibold border border-blue-600 hover:bg-blue-50 transition duration-200">
                            <i class="fas fa-info-circle mr-1"></i>Details
                        </a>
                        <?php if($game->is_published): ?>
                        <a href="<?php echo e(route('games.play', $game)); ?>" class="flex-1 text-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-semibold transition duration-200">
                            <i class="fas fa-play mr-1"></i>Play Now
                        </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full text-center py-12 bg-white rounded-lg shadow">
            <i class="fas fa-gamepad text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">No games available yet</p>
            <?php if(auth()->user()->isTeacher()): ?>
            <a href="<?php echo e(route('games.create')); ?>" class="text-indigo-600 hover:text-indigo-800 font-semibold mt-2 inline-block">
                Create your first game
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if($games->hasPages()): ?>
    <div class="flex justify-center mt-8">
        <?php echo e($games->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\normninja\resources\views/games/index.blade.php ENDPATH**/ ?>