<?php
function get_all_policies($page = 1, $limit = 10, $type_filter = '', $search = '') {
    $db = get_db_connection();
    $offset = ($page - 1) * $limit;

    $sql = "SELECT * FROM policies WHERE 1=1";
    $params = [];

    if (!empty($type_filter)) {
        $sql .= " AND type = :type";
        $params[':type'] = $type_filter;
    }

    if (!empty($search)) {
        $sql .= " AND name LIKE :search";
        $params[':search'] = "%$search%";
    }

    $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_total_policies_count($type_filter = '', $search = '') {
    $db = get_db_connection();
    $sql = "SELECT COUNT(*) FROM policies WHERE 1=1";
    $params = [];

    if (!empty($type_filter)) {
        $sql .= " AND type = :type";
        $params[':type'] = $type_filter;
    }

    if (!empty($search)) {
        $sql .= " AND name LIKE :search";
        $params[':search'] = "%$search%";
    }

    $stmt = $db->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    return $stmt->fetchColumn();
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
