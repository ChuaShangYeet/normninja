<?php $__env->startSection('title', 'Student Performance'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-800">Student Performance Analytics</h1>
        <p class="text-gray-600 mt-2">Monitor student progress and identify those who need support</p>
    </div>

    <!-- Alert for Students Needing Support -->
    <?php
        $studentsNeedingSupport = collect($performance)->filter(function($data) {
            return $data['needs_support'];
        });
    ?>

    <?php if($studentsNeedingSupport->count() > 0): ?>
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-red-800">
                    <?php echo e($studentsNeedingSupport->count()); ?> Student(s) Need Academic Support
                </h3>
                <p class="text-sm text-red-700 mt-1">
                    These students are highlighted below in red and require immediate attention.
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Performance Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Student
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quiz Performance
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Assignment Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $performanceData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="<?php echo e($data['needs_support'] ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-gray-50'); ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                                        <?php echo e(strtoupper(substr($data['student']->name, 0, 1))); ?>

                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo e($data['student']->name); ?>

                                        <?php if($data['needs_support']): ?>
                                            <i class="fas fa-exclamation-circle text-red-500 ml-2" title="Needs Support"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-sm text-gray-500"><?php echo e($data['student']->student_id); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="h-2 rounded-full <?php echo e($data['avg_quiz_score'] >= 60 ? 'bg-green-500' : 'bg-red-500'); ?>" 
                                             style="width: <?php echo e(min($data['avg_quiz_score'], 100)); ?>%"></div>
                                    </div>
                                    <span class="font-semibold <?php echo e($data['avg_quiz_score'] >= 60 ? 'text-green-600' : 'text-red-600'); ?>">
                                        <?php echo e($data['avg_quiz_score']); ?>%
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <?php echo e($data['completed_quizzes']); ?>/<?php echo e($data['total_quizzes']); ?> quizzes completed
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <?php echo e($data['submitted_assignments']); ?>/<?php echo e($data['total_assignments']); ?> submitted
                            </div>
                            <?php if($data['missing_assignments'] > 0): ?>
                                <div class="text-xs text-red-600 font-semibold mt-1">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?php echo e($data['missing_assignments']); ?> missing
                                </div>
                            <?php else: ?>
                                <div class="text-xs text-green-600 font-semibold mt-1">
                                    <i class="fas fa-check-circle"></i>
                                    All submitted
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($data['needs_support']): ?>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-hand-paper mr-1"></i>
                                    Needs Support
                                </span>
                                <div class="mt-2 text-xs text-red-700">
                                    <?php $__currentLoopData = $data['support_reasons']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-start mt-1">
                                            <i class="fas fa-circle text-xs mr-2 mt-1"></i>
                                            <span><?php echo e($reason); ?></span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    On Track
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="<?php echo e(route('teacher.student.detail', $data['student']->id)); ?>" 
                               class="text-indigo-600 hover:text-indigo-900">
                                <i class="fas fa-eye mr-1"></i>
                                View Details
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Support Criteria</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                        <i class="fas fa-chart-line text-red-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-semibold text-gray-900">Low Quiz Performance</h4>
                    <p class="text-sm text-gray-600">Average quiz score below 60%</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                        <i class="fas fa-file-alt text-red-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <h4 class="text-sm font-semibold text-gray-900">Missing Assignments</h4>
                    <p class="text-sm text-gray-600">One or more assignments not submitted</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\normninja\resources\views/teacher/student-performance.blade.php ENDPATH**/ ?>