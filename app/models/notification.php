<?php
function create_notification($user_id, $title, $message, $link = '#') {
    $db = get_db_connection();
    $sql = "INSERT INTO notifications (id, user_id, title, message, link, is_read)
            VALUES (:id, :user_id, :title, :message, :link, 0)";
    $stmt = $db->prepare($sql);
    return $stmt->execute([
        'id' => uniqid('not-'),
        'user_id' => $user_id,
        'title' => $title,
        'message' => $message,
        'link' => $link
    ]);
}

function get_user_notifications($user_id, $limit = 10) {
    $db = get_db_connection();
    $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_unread_count($user_id) {
    $db = get_db_connection();
    $sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0";
    $stmt = $db->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchColumn();
}

function mark_notification_read($id) {
    $db = get_db_connection();
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id";
    $stmt = $db->prepare($sql);
    return $stmt->execute(['id' => $id]);
}

function mark_all_read($user_id) {
    $db = get_db_connection();
    $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    return $stmt->execute(['user_id' => $user_id]);
}
