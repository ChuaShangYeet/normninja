<?php $__env->startSection('title', 'Student Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-md p-6 text-white">
        <h1 class="text-3xl font-bold">Welcome back, <?php echo e(auth()->user()->name); ?>! 🎓</h1>
        <p class="mt-2">Let's continue your learning journey today!</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Completed Quizzes -->
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase">Quizzes Done</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($stats['completed_quizzes']); ?></p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Average Score -->
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase">Avg Score</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($stats['average_quiz_score']); ?>%</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-chart-line text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Games Played -->
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase">Games Played</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($stats['games_played']); ?></p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-gamepad text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Materials Available -->
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase">Materials</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($stats['materials_available']); ?></p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <i class="fas fa-book text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <!-- Active Forums -->
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-pink-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-semibold uppercase">Forums</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo e($stats['active_forums']); ?></p>
                </div>
                <div class="bg-pink-100 rounded-full p-3">
                    <i class="fas fa-comments text-2xl text-pink-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Quiz Attempts -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">Recent Quiz Results</h2>
                <a href="<?php echo e(route('quizzes.index')); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <?php if($recentQuizAttempts->count() > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $recentQuizAttempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800"><?php echo e($attempt->quiz->title ?? 'Quiz Deleted'); ?></h3>
                                <p class="text-sm text-gray-500 mt-1"><?php echo e($attempt->completed_at ? $attempt->completed_at->format('M d, Y') : ''); ?></p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold <?php echo e($attempt->passed ? 'text-green-600' : 'text-red-600'); ?>">
                                    <?php echo e($attempt->percentage); ?>%
                                </div>
                                <div class="text-xs <?php echo e($attempt->passed ? 'text-green-600' : 'text-red-600'); ?>">
                                    <?php echo e($attempt->passed ? 'Passed' : 'Failed'); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-clipboard-list text-gray-300 text-5xl mb-3"></i>
                    <p class="text-gray-500">No quiz attempts yet</p>
                    <a href="<?php echo e(route('quizzes.index')); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold mt-2 inline-block">
                        Take Your First Quiz
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Game Attempts -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">Recent Game Scores</h2>
                <a href="<?php echo e(route('games.index')); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <?php if($recentGameAttempts->count() > 0): ?>
                <div class="space-y-3">
                    <?php $__currentLoopData = $recentGameAttempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded-lg p-4 hover:bg-gray-50 transition duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-800"><?php echo e($attempt->game->title); ?></h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-clock mr-1"></i>
                                    <?php echo e(gmdate('i:s', $attempt->time_spent_seconds)); ?>

                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-purple-600">
                                    <?php echo e($attempt->score); ?>

                                </div>
                                <div class="text-xs text-gray-500">points</div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-gamepad text-gray-300 text-5xl mb-3"></i>
                    <p class="text-gray-500">No games played yet</p>
                    <a href="<?php echo e(route('games.index')); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold mt-2 inline-block">
                        Play Your First Game
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Calendar Section -->
    <div>
        <!-- Schedule Calendar -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-calendar text-indigo-600 mr-2"></i>
                    Schedule Calendar
                </h2>
            </div>
            
            <!-- Calendar Navigation -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800" id="currentMonth"></h3>
                <div class="flex gap-2">
                    <button onclick="previousMonth()" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button onclick="currentMonth()" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                        Today
                    </button>
                    <button onclick="nextMonth()" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 transition">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="border rounded-lg overflow-hidden">
                <!-- Day Headers -->
                <div class="grid grid-cols-7 bg-gray-100">
                    <div class="p-2 text-center text-xs font-semibold text-gray-600">Sun</div>
                    <div class="p-2 text-center text-xs font-semibold text-gray-600">Mon</div>
                    <div class="p-2 text-center text-xs font-semibold text-gray-600">Tue</div>
                    <div class="p-2 text-center text-xs font-semibold text-gray-600">Wed</div>
                    <div class="p-2 text-center text-xs font-semibold text-gray-600">Thu</div>
                    <div class="p-2 text-center text-xs font-semibold text-gray-600">Fri</div>
                    <div class="p-2 text-center text-xs font-semibold text-gray-600">Sat</div>
                </div>
                
                <!-- Calendar Days -->
                <div id="calendarDays" class="grid grid-cols-7">
                    <!-- Days will be populated by JavaScript -->
                </div>
            </div>

            <!-- Add Event Button -->
            <button onclick="openAddEventModal()" class="mt-4 w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i>Add Event
            </button>

            <!-- Events List -->
            <div id="eventsList" class="mt-4 space-y-2 max-h-60 overflow-y-auto">
                <!-- Events will be populated here -->
            </div>
        </div>
    </div>

    <!-- Available Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Learning Materials -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-book text-blue-600 mr-2"></i>
                Learning Materials
            </h2>
            <?php if($availableMaterials->count() > 0): ?>
                <div class="space-y-2">
                    <?php $__currentLoopData = $availableMaterials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $material): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('learning-materials.show', $material)); ?>" class="block p-3 border rounded hover:bg-blue-50 transition duration-200">
                        <div class="font-semibold text-gray-800 text-sm"><?php echo e(Str::limit($material->title, 40)); ?></div>
                        <div class="text-xs text-gray-500 mt-1"><?php echo e($material->subject); ?></div>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <a href="<?php echo e(route('learning-materials.index')); ?>" class="block text-center mt-4 text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                    View All Materials
                </a>
            <?php else: ?>
                <p class="text-gray-500 text-center py-4">No materials available</p>
            <?php endif; ?>
        </div>

        <!-- Available Quizzes -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-question-circle text-green-600 mr-2"></i>
                Available Quizzes
            </h2>
            <?php if($availableQuizzes->count() > 0): ?>
                <div class="space-y-2">
                    <?php $__currentLoopData = $availableQuizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('quizzes.show', $quiz)); ?>" class="block p-3 border rounded hover:bg-green-50 transition duration-200">
                        <div class="font-semibold text-gray-800 text-sm"><?php echo e(Str::limit($quiz->title, 40)); ?></div>
                        <div class="text-xs text-gray-500 mt-1"><?php echo e($quiz->questions->count()); ?> questions</div>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <a href="<?php echo e(route('quizzes.index')); ?>" class="block text-center mt-4 text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                    View All Quizzes
                </a>
            <?php else: ?>
                <p class="text-gray-500 text-center py-4">No quizzes available</p>
            <?php endif; ?>
        </div>

        <!-- Available Games -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-gamepad text-purple-600 mr-2"></i>
                Educational Games
            </h2>
            <?php if($availableGames->count() > 0): ?>
                <div class="space-y-2">
                    <?php $__currentLoopData = $availableGames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $game): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('games.show', $game)); ?>" class="block p-3 border rounded hover:bg-purple-50 transition duration-200">
                        <div class="font-semibold text-gray-800 text-sm"><?php echo e(Str::limit($game->title, 40)); ?></div>
                        <div class="text-xs text-gray-500 mt-1"><?php echo e(ucfirst(str_replace('_', ' ', $game->game_type))); ?></div>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <a href="<?php echo e(route('games.index')); ?>" class="block text-center mt-4 text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                    View All Games
                </a>
            <?php else: ?>
                <p class="text-gray-500 text-center py-4">No games available</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add/Edit Event Modal -->
<div id="eventModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96 shadow-xl">
        <h3 class="text-xl font-bold text-gray-800 mb-4" id="eventModalTitle">Add Event</h3>
        <form id="eventForm" onsubmit="saveEvent(event)">
            <input type="hidden" id="eventId">
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Event Title</label>
                <input type="text" 
                       id="eventTitle" 
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                       required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Event Date</label>
                <input type="date" 
                       id="eventDate" 
                       class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                       required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description (Optional)</label>
                <textarea id="eventDescription" 
                          class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                          rows="3"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Color</label>
                <select id="eventColor" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="#4F46E5">Indigo</option>
                    <option value="#10B981">Green</option>
                    <option value="#F59E0B">Orange</option>
                    <option value="#EF4444">Red</option>
                    <option value="#8B5CF6">Purple</option>
                    <option value="#EC4899">Pink</option>
                </select>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" 
                        onclick="closeEventModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                    Save Event
                </button>
            </div>
        </form>
    </div>
</div>

<!-- CSRF Token for AJAX -->
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<script>
// Calendar functionality with CRUD
let currentDate = new Date();
let events = [];

// Get CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// API endpoints
const API = {
    events: {
        index: '/calendar-events',
        store: '/calendar-events',
        update: (id) => `/calendar-events/${id}`,
        destroy: (id) => `/calendar-events/${id}`
    }
};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadEvents();
    renderCalendar();
});

// ===== CALENDAR EVENTS CRUD =====

async function loadEvents() {
    try {
        const response = await fetch(API.events.index, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            events = await response.json();
            renderCalendar();
            renderEventsList();
        }
    } catch (error) {
        console.error('Error loading events:', error);
        showNotification('Failed to load events', 'error');
    }
}

async function saveEvent(e) {
    e.preventDefault();
    
    const eventId = document.getElementById('eventId').value;
    const data = {
        title: document.getElementById('eventTitle').value,
        date: document.getElementById('eventDate').value,
        description: document.getElementById('eventDescription').value,
        color: document.getElementById('eventColor').value
    };
    
    try {
        const isEdit = eventId !== '';
        const url = isEdit ? API.events.update(eventId) : API.events.store;
        const method = isEdit ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showNotification(result.message, 'success');
            await loadEvents();
            closeEventModal();
        } else {
            showNotification(result.message || 'Failed to save event', 'error');
        }
    } catch (error) {
        console.error('Error saving event:', error);
        showNotification('Failed to save event', 'error');
    }
}

async function deleteEvent(id) {
    if (!confirm('Are you sure you want to delete this event?')) return;
    
    try {
        const response = await fetch(API.events.destroy(id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showNotification(result.message, 'success');
            await loadEvents();
        } else {
            showNotification(result.message || 'Failed to delete event', 'error');
        }
    } catch (error) {
        console.error('Error deleting event:', error);
        showNotification('Failed to delete event', 'error');
    }
}

function editEvent(event) {
    document.getElementById('eventModalTitle').textContent = 'Edit Event';
    document.getElementById('eventId').value = event.id;
    document.getElementById('eventTitle').value = event.title;
    
    // Handle date format (might have timestamp)
    const dateValue = event.date.includes(' ') ? event.date.split(' ')[0] : event.date;
    document.getElementById('eventDate').value = dateValue;
    
    document.getElementById('eventDescription').value = event.description || '';
    document.getElementById('eventColor').value = event.color || '#4F46E5';
    document.getElementById('eventModal').classList.remove('hidden');
}

// ===== RENDERING FUNCTIONS =====

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Update month display
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"];
    document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
    
    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    
    const calendarDays = document.getElementById('calendarDays');
    calendarDays.innerHTML = '';
    
    // Previous month days
    for (let i = firstDay - 1; i >= 0; i--) {
        const day = daysInPrevMonth - i;
        const dayDiv = createDayElement(day, 'text-gray-400 bg-gray-50', null);
        calendarDays.appendChild(dayDiv);
    }
    
    // Current month days
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const isToday = dateStr === today.toISOString().split('T')[0];
        
        // Find events for this day (handle both date formats)
        const dayEvents = events.filter(e => {
            const eventDate = e.date.includes(' ') ? e.date.split(' ')[0] : e.date;
            return eventDate === dateStr;
        });
        
        let className = '';
        if (isToday) {
            className = 'bg-indigo-600 text-white font-bold';
        } else if (dayEvents.length > 0) {
            className = 'bg-blue-50';
        }
        
        const indicator = dayEvents.length > 0 ? `<span class="text-xs block mt-1" style="color: ${dayEvents[0].color}">●</span>` : '';
        const dayDiv = createDayElement(day, className, indicator);
        calendarDays.appendChild(dayDiv);
    }
    
    // Next month days
    const totalCells = calendarDays.children.length;
    const remainingCells = 42 - totalCells;
    for (let day = 1; day <= remainingCells; day++) {
        const dayDiv = createDayElement(day, 'text-gray-400 bg-gray-50', null);
        calendarDays.appendChild(dayDiv);
    }
}

function createDayElement(day, className, indicator) {
    const div = document.createElement('div');
    div.className = `p-3 border text-center text-sm ${className}`;
    div.innerHTML = `${day}${indicator || ''}`;
    return div;
}

function renderEventsList() {
    const eventsList = document.getElementById('eventsList');
    
    if (events.length === 0) {
        eventsList.innerHTML = '<p class="text-gray-400 text-center text-sm">No events scheduled</p>';
        return;
    }
    
    // Sort events by date
    const sortedEvents = [...events].sort((a, b) => {
        const dateA = a.date.includes(' ') ? a.date.split(' ')[0] : a.date;
        const dateB = b.date.includes(' ') ? b.date.split(' ')[0] : b.date;
        return new Date(dateA) - new Date(dateB);
    });
    
    eventsList.innerHTML = sortedEvents.map(event => `
        <div class="p-3 border rounded hover:bg-gray-50 transition" style="border-left: 4px solid ${event.color}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="font-semibold text-gray-800 text-sm">${event.title}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-calendar mr-1"></i>${formatDate(event.date)}
                    </p>
                    ${event.description ? `<p class="text-xs text-gray-600 mt-1">${event.description}</p>` : ''}
                </div>
                <div class="flex gap-2 ml-2">
                    <button onclick='editEvent(${JSON.stringify(event)})' class="text-blue-500 hover:text-blue-700">
                        <i class="fas fa-edit text-sm"></i>
                    </button>
                    <button onclick="deleteEvent(${event.id})" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// ===== MODAL FUNCTIONS =====

function openAddEventModal() {
    document.getElementById('eventModalTitle').textContent = 'Add Event';
    document.getElementById('eventId').value = '';
    document.getElementById('eventTitle').value = '';
    document.getElementById('eventDate').value = '';
    document.getElementById('eventDescription').value = '';
    document.getElementById('eventColor').value = '#4F46E5';
    document.getElementById('eventModal').classList.remove('hidden');
}

function closeEventModal() {
    document.getElementById('eventModal').classList.add('hidden');
}

// ===== NAVIGATION FUNCTIONS =====

function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

function currentMonth() {
    currentDate = new Date();
    renderCalendar();
}

// ===== HELPER FUNCTIONS =====

function formatDate(dateStr) {
    if (!dateStr) return 'No date';
    
    try {
        let cleanDate = dateStr;
        
        // Handle datetime format (2025-11-22 00:00:00)
        if (typeof dateStr === 'string' && dateStr.includes(' ')) {
            cleanDate = dateStr.split(' ')[0];
        }
        
        // Handle ISO format (2025-11-22T00:00:00Z)
        if (typeof dateStr === 'string' && dateStr.includes('T')) {
            cleanDate = dateStr.split('T')[0];
        }
        
        const date = new Date(cleanDate + 'T00:00:00');
        
        if (isNaN(date.getTime())) {
            return 'Invalid Date';
        }
        
        const options = { month: 'short', day: 'numeric', year: 'numeric' };
        return date.toLocaleDateString('en-US', options);
        
    } catch (error) {
        return 'Invalid Date';
    }
}

function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\normninja\resources\views/student/dashboard.blade.php ENDPATH**/ ?>