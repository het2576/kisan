<?php 
session_start();
$lang = $_SESSION['lang'] ?? 'en';
?>

<!-- Add this debug div at the top -->
<div id="notificationDebug" style="display: none; position: fixed; bottom: 0; right: 0; background: rgba(0,0,0,0.8); color: white; padding: 10px; max-width: 300px; z-index: 9999;"></div>

<div class="notification-wrapper">
    <div class="notification-bell" id="notificationBell">
        <i class="fas fa-bell"></i>
        <span class="notification-badge" id="notificationCount">0</span>
        <span class="notification-dot" id="notificationDot"></span>
    </div>
    
    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <h6 id="notificationTitle">Notifications</h6>
            <button class="mark-all-read" id="markAllRead">Mark all as read</button>
        </div>
        <div class="notification-list" id="notificationList">
            <!-- Notifications will be loaded here -->
        </div>
    </div>
</div>

<style>
.notification-wrapper {
    position: relative;
    margin-right: 1.5rem;
    z-index: 9999;
}

.notification-bell {
    position: relative;
    cursor: pointer;
    padding: 0.5rem;
    font-size: 1.25rem;
    color: #2F855A;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.notification-bell:hover {
    transform: scale(1.1);
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #E53E3E;
    color: white;
    border-radius: 50%;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    z-index: 1;
}

.notification-dot {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 8px;
    height: 8px;
    background-color: #E53E3E;
    border-radius: 50%;
    display: none;
    z-index: 1;
}

.notification-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: -10px;
    width: 320px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    display: none;
    z-index: 9999;
    border: 1px solid #E2E8F0;
    max-height: 400px;
    overflow-y: auto;
}

.notification-dropdown::before {
    content: '';
    position: absolute;
    top: -6px;
    right: 20px;
    width: 12px;
    height: 12px;
    background: white;
    transform: rotate(45deg);
    border-left: 1px solid #E2E8F0;
    border-top: 1px solid #E2E8F0;
}

.notification-header {
    padding: 1rem;
    border-bottom: 1px solid #E2E8F0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    background: white;
    z-index: 1;
}

.notification-header h6 {
    margin: 0;
    font-weight: 600;
    color: #2D3748;
}

.mark-all-read {
    background: none;
    border: none;
    color: #2F855A;
    font-size: 0.875rem;
    cursor: pointer;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.mark-all-read:hover {
    background: #E6FFFA;
}

.notification-list {
    padding: 0.5rem;
    background: white;
}

.notification-item {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid transparent;
    position: relative;
    background: white;
}

.notification-item:hover {
    background: #E6FFFA;
    border-color: #E2E8F0;
}

.notification-item.unread {
    background: #E6FFFA;
}

.notification-item.unread::after {
    content: '';
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 8px;
    height: 8px;
    background-color: #E53E3E;
    border-radius: 50%;
}

.notification-item .title {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #2D3748;
    padding-right: 20px;
}

.notification-item .message {
    font-size: 0.875rem;
    color: #4A5568;
    opacity: 0.8;
}

.notification-item .time {
    font-size: 0.75rem;
    color: #718096;
    margin-top: 0.5rem;
}

.notification-item i {
    margin-right: 0.5rem;
    color: #2F855A;
}

@keyframes bellShake {
    0% { transform: rotate(0); }
    15% { transform: rotate(5deg); }
    30% { transform: rotate(-5deg); }
    45% { transform: rotate(4deg); }
    60% { transform: rotate(-4deg); }
    75% { transform: rotate(2deg); }
    85% { transform: rotate(-2deg); }
    92% { transform: rotate(1deg); }
    100% { transform: rotate(0); }
}

.shake {
    animation: bellShake 0.8s cubic-bezier(.36,.07,.19,.97) both;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.notification-item {
    animation: fadeIn 0.3s ease-out;
}

/* Debug styles */
#notificationDebug {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 15px;
    border-radius: 8px;
    max-width: 400px;
    z-index: 10000;
    font-family: monospace;
    font-size: 12px;
    white-space: pre-wrap;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bell = document.getElementById('notificationBell');
    const dropdown = document.getElementById('notificationDropdown');
    const notificationList = document.getElementById('notificationList');
    const countBadge = document.getElementById('notificationCount');
    const notificationDot = document.getElementById('notificationDot');
    const notificationTitle = document.getElementById('notificationTitle');
    const markAllReadBtn = document.getElementById('markAllRead');
    const debugDiv = document.getElementById('notificationDebug');
    let isDropdownOpen = false;
    let lastNotificationCount = 0;
    let refreshInterval;
    let countCheckInterval;

    // Debug function with timestamp
    function showDebug(message, data = null) {
        const timestamp = new Date().toISOString();
        console.log(`[${timestamp}] ${message}`, data);
        debugDiv.style.display = 'block';
        debugDiv.innerHTML = `<pre>[${timestamp}]\n${message}\n${data ? JSON.stringify(data, null, 2) : ''}</pre>`;
        setTimeout(() => {
            debugDiv.style.display = 'none';
        }, 5000);
    }

    // Enhanced error handling
    async function handleFetchResponse(response) {
        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
        }
        const contentType = response.headers.get('content-type');
        try {
            const data = contentType && contentType.includes('application/json') 
                ? await response.json() 
                : await response.text();
            if (data.error) {
                throw new Error(data.error);
            }
            return data;
        } catch (error) {
            throw new Error(`Parse error: ${error.message}`);
        }
    }

    // Function to update notification count with retry mechanism
    async function updateNotificationCount(retryCount = 3) {
        try {
            const response = await fetch('notifications.php?action=get_count');
            const data = await handleFetchResponse(response);
            showDebug('Count response:', data);
            
            if (data.success) {
                const count = parseInt(data.count) || 0;
                countBadge.textContent = count;
                countBadge.style.display = count > 0 ? 'flex' : 'none';
                
                if (count > lastNotificationCount) {
                    notificationDot.style.display = 'block';
                    bell.classList.add('shake');
                    if (isDropdownOpen) {
                        await loadNotifications(); // Refresh list if dropdown is open
                    }
                }
                
                lastNotificationCount = count;
            }
        } catch (error) {
            showDebug('Error updating count:', error.message);
            if (retryCount > 0) {
                setTimeout(() => updateNotificationCount(retryCount - 1), 2000);
            }
        }
    }

    // Enhanced notification loading with retry
    async function loadNotifications(retryCount = 3) {
        try {
            notificationList.innerHTML = '<div class="notification-item"><div class="message">Loading...</div></div>';
            
            const response = await fetch('notifications.php?action=get_notifications');
            const data = await handleFetchResponse(response);
            showDebug('Notifications response:', data);
            
            if (data.success) {
                const notifications = data.notifications;
                const translations = data.translations;
                
                // Update translations
                if (translations) {
                    notificationTitle.textContent = translations.notifications;
                    markAllReadBtn.textContent = translations.mark_all_read;
                }
                
                if (!notifications || notifications.length === 0) {
                    notificationList.innerHTML = `
                        <div class="notification-item">
                            <div class="message">${translations.no_notifications}</div>
                        </div>
                    `;
                    return;
                }
                
                notificationList.innerHTML = notifications.map(notification => `
                    <div class="notification-item ${!notification.is_read ? 'unread' : ''}" 
                         data-id="${notification.id}">
                        <i class="fas ${notification.icon}"></i>
                        <div class="title">${notification.title}</div>
                        <div class="message">${notification.message}</div>
                        <div class="time">${timeAgo(notification.created_at)}</div>
                    </div>
                `).join('');
                
                await updateNotificationCount();
            }
        } catch (error) {
            showDebug('Error loading notifications:', error.message);
            if (retryCount > 0) {
                setTimeout(() => loadNotifications(retryCount - 1), 2000);
            } else {
                notificationList.innerHTML = `
                    <div class="notification-item">
                        <div class="message">Error loading notifications: ${error.message}</div>
                    </div>
                `;
            }
        }
    }

    // Improved time formatting with language support
    function timeAgo(date) {
        const seconds = Math.floor((new Date() - new Date(date)) / 1000);
        const intervals = {
            year: 31536000,
            month: 2592000,
            day: 86400,
            hour: 3600,
            minute: 60,
            second: 1
        };
        
        for (const [unit, secondsInUnit] of Object.entries(intervals)) {
            const interval = seconds / secondsInUnit;
            if (interval > 1) {
                const count = Math.floor(interval);
                // You can add translations for time units here
                return `${count} ${unit}${count !== 1 ? 's' : ''} ago`;
            }
        }
        return 'just now';
    }

    // Toggle dropdown with improved state management
    bell.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();
        isDropdownOpen = !isDropdownOpen;
        dropdown.style.display = isDropdownOpen ? 'block' : 'none';
        
        if (isDropdownOpen) {
            await loadNotifications();
            bell.classList.remove('shake');
            notificationDot.style.display = 'none';
            
            // Clear existing intervals
            clearInterval(refreshInterval);
            clearInterval(countCheckInterval);
            
            // Start refresh interval when dropdown is opened
            refreshInterval = setInterval(() => loadNotifications(), 10000); // Check every 10 seconds when open
        } else {
            // Clear refresh interval when dropdown is closed
            clearInterval(refreshInterval);
            
            // Start count check interval
            countCheckInterval = setInterval(() => updateNotificationCount(), 30000); // Check every 30 seconds when closed
        }
    });

    // Improved click outside handling
    document.addEventListener('click', (e) => {
        if (!bell.contains(e.target) && !dropdown.contains(e.target)) {
            isDropdownOpen = false;
            dropdown.style.display = 'none';
            clearInterval(refreshInterval);
            countCheckInterval = setInterval(() => updateNotificationCount(), 30000);
        }
    });

    // Prevent dropdown from closing when clicking inside
    dropdown.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    // Enhanced mark as read functionality
    notificationList.addEventListener('click', async (e) => {
        const item = e.target.closest('.notification-item');
        if (item && item.classList.contains('unread')) {
            try {
                const notificationId = item.dataset.id;
                const formData = new FormData();
                formData.append('notification_id', notificationId);

                item.style.opacity = '0.5'; // Visual feedback
                
                const response = await fetch('notifications.php?action=mark_read', {
                    method: 'POST',
                    body: formData
                });
                const data = await handleFetchResponse(response);
                showDebug('Mark read response:', data);
                
                if (data.success) {
                    item.classList.remove('unread');
                    item.style.opacity = '1';
                    await updateNotificationCount();
                    await loadNotifications();
                }
            } catch (error) {
                showDebug('Error marking as read:', error.message);
                item.style.opacity = '1'; // Reset opacity on error
            }
        }
    });

    // Enhanced mark all as read functionality
    markAllReadBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();
        try {
            markAllReadBtn.disabled = true; // Prevent double-clicks
            
            const response = await fetch('notifications.php?action=mark_all_read');
            const data = await handleFetchResponse(response);
            showDebug('Mark all read response:', data);
            
            if (data.success) {
                await loadNotifications();
                await updateNotificationCount();
                notificationDot.style.display = 'none';
            }
        } catch (error) {
            showDebug('Error marking all as read:', error.message);
        } finally {
            markAllReadBtn.disabled = false;
        }
    });

    // Initial load with retry mechanism
    (async function initialLoad() {
        try {
            await updateNotificationCount();
            countCheckInterval = setInterval(() => updateNotificationCount(), 30000);
        } catch (error) {
            showDebug('Error in initial load:', error.message);
            setTimeout(initialLoad, 2000);
        }
    })();

    // Debug initial load
    showDebug('Notification component initialized');
});
</script> 