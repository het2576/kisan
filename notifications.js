// Function to check for new notifications
function checkNotifications() {
    fetch('check_notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
            }
        })
        .catch(error => console.error('Error checking notifications:', error));
}

// Function to load notifications
function loadNotifications() {
    fetch('get_notifications.php')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.unread_count);
            updateNotificationList(data.notifications);
        })
        .catch(error => console.error('Error loading notifications:', error));
}

// Function to update notification badge
function updateNotificationBadge(count) {
    const badge = document.getElementById('notification-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'block' : 'none';
    }
}

// Function to update notification list
function updateNotificationList(notifications) {
    const container = document.getElementById('notification-list');
    if (!container) return;

    container.innerHTML = '';
    
    if (notifications.length === 0) {
        container.innerHTML = '<div class="no-notifications">No notifications</div>';
        return;
    }

    notifications.forEach(notification => {
        const element = createNotificationElement(notification);
        container.appendChild(element);
    });
}

// Function to create notification element
function createNotificationElement(notification) {
    const div = document.createElement('div');
    div.className = `notification ${notification.is_read ? 'read' : 'unread'}`;
    div.setAttribute('data-id', notification.id);

    const icon = getNotificationIcon(notification.type);
    const date = new Date(notification.created_at).toLocaleString();

    div.innerHTML = `
        <div class="notification-icon">${icon}</div>
        <div class="notification-content">
            <div class="notification-header">
                <span class="notification-title">${notification.title}</span>
                <span class="notification-time">${date}</span>
            </div>
            <div class="notification-message">${notification.message}</div>
        </div>
    `;

    if (!notification.is_read) {
        div.addEventListener('click', () => markNotificationAsRead(notification.id));
    }

    return div;
}

// Function to get notification icon
function getNotificationIcon(type) {
    const icons = {
        'inventory': '📦',
        'market': '💰',
        'weather': '🌤️'
    };
    return icons[type] || '📢';
}

// Function to mark notification as read
function markNotificationAsRead(notificationId) {
    const formData = new FormData();
    formData.append('notification_id', notificationId);

    fetch('mark_notification_read.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
}

// Check for new notifications every 5 minutes
setInterval(checkNotifications, 5 * 60 * 1000);

// Initial load
document.addEventListener('DOMContentLoaded', () => {
    loadNotifications();
}); 