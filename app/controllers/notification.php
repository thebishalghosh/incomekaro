<?php
require_once APP_PATH . '/models/notification.php';

function notification_index() {
    require_login();
    $notifications = get_user_notifications($_SESSION['user_id'], 50);
    view('dashboard/notifications_list', ['notifications' => $notifications]);
}

function notification_read($id) {
    require_login();

    // Fetch notification to get the link
    $db = get_db_connection();
    $stmt = $db->prepare("SELECT link, user_id FROM notifications WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $notif = $stmt->fetch();

    if ($notif && $notif['user_id'] === $_SESSION['user_id']) {
        mark_notification_read($id);
        redirect($notif['link']);
    } else {
        redirect('dashboard/index');
    }
}

function notification_mark_all_read() {
    require_login();
    mark_all_read($_SESSION['user_id']);
    redirect('notification/index');
}

// API Endpoint for Polling
function notification_count() {
    // Suppress HTML output, return JSON only
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['count' => 0]);
        exit;
    }

    // Debug Logging (Enable if needed for troubleshooting)
    // error_log("Notification Poll: User " . $_SESSION['user_id']);

    // 1. Rate Limiting (Session-based)
    // Prevents excessive calls from the same session (e.g., multiple tabs or aggressive retries)
    $rate_limit_window = 10; // seconds
    $last_request_time = $_SESSION['notif_poll_last_time'] ?? 0;
    $current_time = time();

    if (($current_time - $last_request_time) < $rate_limit_window) {
        // Too many requests - Return 429
        header('HTTP/1.1 429 Too Many Requests');
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Too many requests', 'retry_after' => $rate_limit_window]);
        exit;
    }

    // Update last request time immediately to lock out concurrent requests
    $_SESSION['notif_poll_last_time'] = $current_time;

    // 2. Caching (Session-based)
    // Serves cached count to reduce DB load
    $cache_duration = 30; // seconds
    $cached_count = $_SESSION['notif_count_cache'] ?? null;
    $cache_time = $_SESSION['notif_count_cache_time'] ?? 0;

    if ($cached_count !== null && ($current_time - $cache_time) < $cache_duration) {
        // Return cached value
        // error_log("Serving from cache");
        header('Content-Type: application/json');
        echo json_encode(['count' => $cached_count]);
        exit;
    }

    // 3. Fetch from DB
    // Only runs if cache expired or not set
    $count = get_unread_count($_SESSION['user_id']);

    // Update Cache
    $_SESSION['notif_count_cache'] = $count;
    $_SESSION['notif_count_cache_time'] = $current_time;

    header('Content-Type: application/json');
    echo json_encode(['count' => $count]);
    exit;
}
