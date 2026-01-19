<?php
function get_all_policies() {
    $db = get_db_connection();
    $stmt = $db->query("SELECT * FROM policies ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function create_policy($data) {
    $db = get_db_connection();
    $sql = "INSERT INTO policies (id, name, type, file_url) VALUES (:id, :name, :type, :file_url)";
    $stmt = $db->prepare($sql);
    return $stmt->execute([
        'id' => uniqid('pol-'),
        'name' => $data['name'],
        'type' => $data['type'],
        'file_url' => $data['file_url']
    ]);
}

function delete_policy($id) {
    $db = get_db_connection();

    // Get file path to delete file
    $stmt = $db->prepare("SELECT file_url FROM policies WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $file_url = $stmt->fetchColumn();

    if ($file_url && file_exists(APP_ROOT . '/public/' . $file_url)) {
        unlink(APP_ROOT . '/public/' . $file_url);
    }

    $stmt = $db->prepare("DELETE FROM policies WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}
