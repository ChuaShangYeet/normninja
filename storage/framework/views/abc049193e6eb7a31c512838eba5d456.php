<?php $__env->startSection('title', 'Learning Materials'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Learning Materials</h1>
            <p class="text-gray-600 mt-2">
                <?php if(auth()->user()->isTeacher()): ?>
                    Upload and manage learning resources for your students
                <?php else: ?>
                    Access study materials and resources
                <?php endif; ?>
            </p>
        </div>
        <?php if(auth()->user()->isTeacher()): ?>
        <a href="<?php echo e(route('learning-materials.create')); ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md transition duration-200">
            <i class="fas fa-plus mr-2"></i>Upload Material
        </a>
        <?php endif; ?>
    </div>

    <!-- Search Bar (Student Only) -->
    <?php if(!auth()->user()->isTeacher()): ?>
    <div class="mb-6">
        <form action="<?php echo e(route('learning-materials.index')); ?>" method="GET">
            <div class="flex">
                <input
                    type="text"
                    name="search"
                    class="w-full border border-gray-300 rounded-l-lg px-4 py-2 focus:ring-purple-500 focus:border-purple-500"
                    placeholder="Search materials..."
                    value="<?php echo e(request('search')); ?>"
                >
                <button
                    class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-r-lg font-semibold transition duration-200">
                    Search
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Success Message -->
    <?php if(session('success')): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <p><?php echo e(session('success')); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Student Helper Text -->
    <?php if(auth()->user()->isStudent() && $materials->count() > 0): ?>
    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded">
        <div class="flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            <p><strong>Tip:</strong> Click on any material card or the "View & Download" button to access your learning materials.</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Materials Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition duration-300 overflow-hidden <?php echo e(auth()->user()->isStudent() ? 'cursor-pointer' : ''); ?>"
             <?php if(auth()->user()->isStudent()): ?> onclick="window.location='<?php echo e(route('learning-materials.show', $material)); ?>'" <?php endif; ?>>
            <!-- Material Type Header -->
            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-4 text-white">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold uppercase tracking-wide">
                        <?php
                            $fileIcon = 'fa-file';
                            $fileColor = 'text-white';
                            switch($material->file_type) {
                                case 'pdf':
                                    $fileIcon = 'fa-file-pdf';
                                    break;
                                case 'doc':
                                case 'docx':
                                    $fileIcon = 'fa-file-word';
                                    break;
                                case 'ppt':
                                case 'pptx':
                                    $fileIcon = 'fa-file-powerpoint';
                                    break;
                                case 'mp4':
                                case 'avi':
                                case 'mov':
                                    $fileIcon = 'fa-file-video';
                                    break;
                            }
                        ?>
                        <i class="fas <?php echo e($fileIcon); ?> mr-2"></i><?php echo e(strtoupper($material->file_type)); ?>

                    </span>
                    <?php if(!$material->is_published): ?>
                    <span class="bg-yellow-400 text-yellow-900 px-2 py-1 rounded text-xs font-semibold">
                        Draft
                    </span>
                    <?php endif; ?>
                </div>
                <h3 class="font-bold text-lg"><?php echo e($material->title); ?></h3>
                <?php if($material->subject): ?>
                <p class="text-sm text-purple-100 mt-1">
                    <i class="fas fa-book mr-1"></i><?php echo e($material->subject); ?>

                </p>
                <?php endif; ?>
            </div>

            <!-- Material Body -->
            <div class="p-4">
                <!-- Description -->
                <?php if($material->description): ?>
                <p class="text-gray-600 mb-4 text-sm line-clamp-3"><?php echo e($material->description); ?></p>
                <?php endif; ?>

                <!-- Material Info -->
                <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
                    <div class="bg-gray-50 rounded p-2">
                        <p class="text-gray-500 text-xs">Uploaded By</p>
                        <p class="font-semibold text-gray-800 truncate"><?php echo e($material->teacher->name); ?></p>
                    </div>
                    <?php if($material->grade_level): ?>
                    <div class="bg-gray-50 rounded p-2">
                        <p class="text-gray-500 text-xs">Grade Level</p>
                        <p class="font-semibold text-gray-800">Grade <?php echo e($material->grade_level); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Upload Date -->
                <div class="border-t pt-3 mb-3 text-xs text-gray-500">
                    <p><i class="fas fa-calendar mr-1"></i><?php echo e($material->created_at->format('M d, Y')); ?></p>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <?php if(auth()->user()->isTeacher()): ?>
                        <a href="<?php echo e(route('learning-materials.show', $material)); ?>"
                           class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-sm font-semibold transition duration-200"
                           onclick="event.stopPropagation();">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        <a href="<?php echo e(route('learning-materials.edit', $material)); ?>"
                           class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded text-sm font-semibold transition duration-200"
                           onclick="event.stopPropagation();">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="<?php echo e(route('learning-materials.destroy', $material)); ?>" method="POST" class="inline" onsubmit="event.stopPropagation(); return confirm('Are you sure you want to delete this material?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm font-semibold transition duration-200">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo e(route('learning-materials.show', $material)); ?>"
                           class="flex-1 text-center bg-purple-600 hover:bg-purple-700 text-white px-4 py-3 rounded-lg text-sm font-bold transition duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                           onclick="event.stopPropagation();">
                            <i class="fas fa-download mr-2"></i>View & Download
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full text-center py-12 bg-white rounded-lg shadow">
            <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">No learning materials available yet</p>
            <?php if(auth()->user()->isTeacher()): ?>
            <a href="<?php echo e(route('learning-materials.create')); ?>" class="text-purple-600 hover:text-purple-800 font-semibold mt-2 inline-block">
                Upload your first material
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if($materials->hasPages()): ?>
    <div class="flex justify-center mt-8">
        <?php echo e($materials->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\normninja\resources\views/learning-materials/index.blade.php ENDPATH**/ ?>