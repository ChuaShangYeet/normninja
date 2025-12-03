

<?php $__env->startSection('title', $learningMaterial->title); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <a href="<?php echo e(route('learning-materials.index')); ?>" class="text-purple-600 hover:text-purple-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Materials
            </a>
            
            <?php if(auth()->user()->isTeacher() && auth()->id() === $learningMaterial->teacher_id): ?>
            <div class="flex gap-2">
                <a href="<?php echo e(route('learning-materials.edit', $learningMaterial)); ?>" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <form action="<?php echo e(route('learning-materials.destroy', $learningMaterial)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this material?');">
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
            <!-- Material Info Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <!-- Header -->
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold uppercase tracking-wide">
                            <?php
                                $fileIcon = 'fa-file';
                                switch($learningMaterial->file_type) {
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
                            <i class="fas <?php echo e($fileIcon); ?> mr-2"></i><?php echo e(strtoupper($learningMaterial->file_type)); ?>

                        </span>
                        <?php if(!$learningMaterial->is_published): ?>
                        <span class="bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-bold">
                            DRAFT
                        </span>
                        <?php else: ?>
                        <span class="bg-white text-purple-600 px-3 py-1 rounded-full text-xs font-bold">
                            PUBLISHED
                        </span>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-3xl font-bold"><?php echo e($learningMaterial->title); ?></h1>
                    <?php if($learningMaterial->subject): ?>
                    <p class="text-purple-100 mt-2">
                        <i class="fas fa-book mr-2"></i><?php echo e($learningMaterial->subject); ?>

                    </p>
                    <?php endif; ?>
                </div>

                <!-- Details -->
                <div class="p-6">
                    <?php if($learningMaterial->description): ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                        <p class="text-gray-600"><?php echo e($learningMaterial->description); ?></p>
                    </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Uploaded By</p>
                            <p class="font-semibold text-gray-800"><?php echo e($learningMaterial->teacher->name); ?></p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Upload Date</p>
                            <p class="font-semibold text-gray-800"><?php echo e($learningMaterial->created_at->format('M d, Y')); ?></p>
                        </div>
                        <?php if($learningMaterial->grade_level): ?>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">Grade Level</p>
                            <p class="font-semibold text-gray-800">Grade <?php echo e($learningMaterial->grade_level); ?></p>
                        </div>
                        <?php endif; ?>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-500 mb-1">File Type</p>
                            <p class="font-semibold text-gray-800"><?php echo e(strtoupper($learningMaterial->file_type)); ?></p>
                        </div>
                    </div>

                    <!-- Download Button -->
                    <div class="border-t pt-6">
                        <a href="<?php echo e(Storage::url($learningMaterial->file_path)); ?>" 
                           download
                           class="inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-bold text-lg transition duration-200">
                            <i class="fas fa-download mr-3"></i>Download Material
                        </a>
                    </div>
                </div>
            </div>

            <!-- Preview Section (for PDFs and Videos) -->
            <?php if(in_array($learningMaterial->file_type, ['pdf', 'mp4', 'avi', 'mov'])): ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-eye mr-2 text-purple-600"></i>Preview
                </h2>
                
                <?php if($learningMaterial->file_type === 'pdf'): ?>
                <div class="border rounded-lg overflow-hidden" style="height: 600px;">
                    <iframe src="<?php echo e(Storage::url($learningMaterial->file_path)); ?>" 
                            class="w-full h-full"
                            frameborder="0">
                    </iframe>
                </div>
                <?php elseif(in_array($learningMaterial->file_type, ['mp4', 'avi', 'mov'])): ?>
                <div class="rounded-lg overflow-hidden bg-black">
                    <video controls class="w-full">
                        <source src="<?php echo e(Storage::url($learningMaterial->file_path)); ?>" type="video/<?php echo e($learningMaterial->file_type); ?>">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 sticky top-4">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-bolt mr-2 text-purple-600"></i>Quick Actions
                </h3>
                
                <div class="space-y-3">
                    <a href="<?php echo e(Storage::url($learningMaterial->file_path)); ?>" 
                       download
                       class="block w-full bg-purple-600 hover:bg-purple-700 text-white text-center px-4 py-3 rounded-lg font-semibold transition duration-200">
                        <i class="fas fa-download mr-2"></i>Download
                    </a>
                    
                    <a href="<?php echo e(Storage::url($learningMaterial->file_path)); ?>" 
                       target="_blank"
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-3 rounded-lg font-semibold transition duration-200">
                        <i class="fas fa-external-link-alt mr-2"></i>Open in New Tab
                    </a>
                    
                    <?php if(auth()->user()->isStudent()): ?>
                    <button onclick="alert('Bookmark feature coming soon!')" 
                            class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center px-4 py-3 rounded-lg font-semibold transition duration-200">
                        <i class="fas fa-bookmark mr-2"></i>Bookmark
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Material Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-info-circle mr-2 text-purple-600"></i>Information
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">File Name</p>
                        <p class="font-semibold text-gray-800 text-sm break-all"><?php echo e(basename($learningMaterial->file_path)); ?></p>
                    </div>
                    
                    <div class="pt-4 border-t">
                        <p class="text-sm text-gray-600 mb-1">Created</p>
                        <p class="font-semibold text-gray-800"><?php echo e($learningMaterial->created_at->format('M d, Y')); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($learningMaterial->created_at->format('h:i A')); ?></p>
                    </div>
                    
                    <div class="pt-4 border-t">
                        <p class="text-sm text-gray-600 mb-1">Last Modified</p>
                        <p class="font-semibold text-gray-800"><?php echo e($learningMaterial->updated_at->format('M d, Y')); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($learningMaterial->updated_at->diffForHumans()); ?></p>
                    </div>

                    <div class="pt-4 border-t">
                        <p class="text-sm text-gray-600 mb-1">Status</p>
                        <?php if($learningMaterial->is_published): ?>
                        <span class="inline-flex items-center bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                            <i class="fas fa-check-circle mr-2"></i>Published
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-semibold">
                            <i class="fas fa-clock mr-2"></i>Draft
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-3">
                    <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>Study Tips
                </h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-2 mt-1"></i>
                        <span>Download for offline access</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-2 mt-1"></i>
                        <span>Take notes while studying</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-2 mt-1"></i>
                        <span>Review regularly for retention</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-purple-600 mr-2 mt-1"></i>
                        <span>Ask questions if unclear</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\normninja\resources\views/learning-materials/show.blade.php ENDPATH**/ ?>