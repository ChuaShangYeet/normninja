

<?php $__env->startSection('title', 'Discussion Forums'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Discussion Forums</h1>
            <p class="text-gray-600 mt-2">
                <?php if(auth()->user()->isTeacher()): ?>
                    Create and manage discussion forums for your students
                <?php else: ?>
                    Join discussions and collaborate with your classmates
                <?php endif; ?>
            </p>
        </div>
        <?php if(auth()->user()->isTeacher()): ?>
        <a href="<?php echo e(route('forums.create')); ?>" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md transition duration-200">
            <i class="fas fa-plus mr-2"></i>Create Forum
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

    <!-- Forums List -->
    <div class="space-y-4">
        <?php $__empty_1 = true; $__currentLoopData = $forums; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $forum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300 overflow-hidden">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <!-- Forum Info -->
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="bg-pink-100 rounded-full p-3">
                                <i class="fas fa-comments text-2xl text-pink-600"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">
                                    <a href="<?php echo e(route('forums.show', $forum)); ?>" class="hover:text-pink-600 transition">
                                        <?php echo e($forum->title); ?>

                                    </a>
                                </h3>
                                <?php if($forum->subject): ?>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-book mr-1"></i><?php echo e($forum->subject); ?>

                                </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if($forum->description): ?>
                        <p class="text-gray-600 mb-3 ml-16"><?php echo e($forum->description); ?></p>
                        <?php endif; ?>

                        <!-- Forum Meta -->
                        <div class="flex items-center gap-6 ml-16 text-sm text-gray-500">
                            <div class="flex items-center">
                                <i class="fas fa-user mr-2 text-gray-400"></i>
                                <span><?php echo e($forum->teacher->name); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                <span><?php echo e($forum->created_at->format('M d, Y')); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-comment-dots mr-2 text-gray-400"></i>
                                <span><?php echo e($forum->posts()->count()); ?> posts</span>
                            </div>
                            <?php if(!$forum->is_active): ?>
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-lock mr-1"></i>Closed
                            </span>
                            <?php else: ?>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2 ml-4">
                        <?php if(auth()->user()->isTeacher() && auth()->id() === $forum->teacher_id): ?>
                        <a href="<?php echo e(route('forums.edit', $forum)); ?>" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200 text-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="<?php echo e(route('forums.destroy', $forum)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this forum? All posts will be deleted.');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200 text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                        
                        <?php if($forum->is_active || auth()->user()->isTeacher()): ?>
                        <a href="<?php echo e(route('forums.show', $forum)); ?>" 
                           class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200 text-sm">
                            <i class="fas fa-arrow-right mr-1"></i>View
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Latest Post Preview -->
                <?php
                    $latestPost = $forum->posts()->with('user')->latest()->first();
                ?>
                <?php if($latestPost): ?>
                <div class="mt-4 pt-4 border-t ml-16">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold text-sm">
                            <?php echo e(strtoupper(substr($latestPost->user->name, 0, 2))); ?>

                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-gray-800 text-sm"><?php echo e($latestPost->user->name); ?></span>
                                <span class="text-xs text-gray-500"><?php echo e($latestPost->created_at->diffForHumans()); ?></span>
                            </div>
                            <p class="text-gray-600 text-sm line-clamp-2"><?php echo e($latestPost->content); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center py-12 bg-white rounded-lg shadow">
            <i class="fas fa-comments text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">No forums available yet</p>
            <?php if(auth()->user()->isTeacher()): ?>
            <a href="<?php echo e(route('forums.create')); ?>" class="text-pink-600 hover:text-pink-800 font-semibold mt-2 inline-block">
                Create your first forum
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if($forums->hasPages()): ?>
    <div class="flex justify-center mt-8">
        <?php echo e($forums->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\normninja\resources\views/forums/index.blade.php ENDPATH**/ ?>