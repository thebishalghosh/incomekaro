<?php
function get_all_services() {
    $db = get_db_connection();
    $sql = "SELECT * FROM services ORDER BY category, name";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function get_service_by_id($id) {
    $db = get_db_connection();
    $sql = "SELECT * FROM services WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    return $stmt->fetch();
}

function create_service($data) {
    $db = get_db_connection();
    $sql = "INSERT INTO services (id, name, description, url, image_url, category, is_active)
            VALUES (:id, :name, :description, :url, :image_url, :category, :is_active)";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $data['id']);
    $stmt->bindValue(':name', $data['name']);
    $stmt->bindValue(':description', $data['description']);
    $stmt->bindValue(':url', $data['url']);
    $stmt->bindValue(':image_url', $data['image_url']);
    $stmt->bindValue(':category', $data['category']);
    $stmt->bindValue(':is_active', $data['is_active']);

    return $stmt->execute();
}

function update_service($data) {
    $db = get_db_connection();
    $sql = "UPDATE services SET
            name = :name,
            description = :description,
            url = :url,
            category = :category,
            is_active = :is_active" .
            ($data['image_url'] ? ", image_url = :image_url" : "") .
            " WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $data['id']);
    $stmt->bindValue(':name', $data['name']);
    $stmt->bindValue(':description', $data['description']);
    $stmt->bindValue(':url', $data['url']);
    $stmt->bindValue(':category', $data['category']);
    $stmt->bindValue(':is_active', $data['is_active']);
    if ($data['image_url']) {
        $stmt->bindValue(':image_url', $data['image_url']);
    }

    return $stmt->execute();
}

function delete_service($id) {
    $db = get_db_connection();
    $sql = "DELETE FROM services WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    return $stmt->execute();
}
