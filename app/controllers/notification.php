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
        echo json_encode(['count' => 0]);
        exit;
    }

    $count = get_unread_count($_SESSION['user_id']);

    header('Content-Type: application/json');
    echo json_encode(['count' => $count]);
    exit;
}
