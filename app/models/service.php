<?php
function get_all_services() {
    $db = get_db_connection();
    $sql = "SELECT s.*, p.name as parent_name
            FROM services s
            LEFT JOIN services p ON s.parent_id = p.id
            ORDER BY s.category, s.name";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function get_top_level_services() {
    $db = get_db_connection();
    $sql = "SELECT * FROM services WHERE parent_id IS NULL ORDER BY name";
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

function get_child_services($parent_id) {
    $db = get_db_connection();
    $sql = "SELECT * FROM services WHERE parent_id = :parent_id AND is_active = 1 ORDER BY name";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':parent_id', $parent_id);
    $stmt->execute();
    return $stmt->fetchAll();
}

function create_service($data) {
    $db = get_db_connection();
    $sql = "INSERT INTO services (id, name, description, url, image_url, category, is_active, service_type, parent_id, form_type)
            VALUES (:id, :name, :description, :url, :image_url, :category, :is_active, :service_type, :parent_id, :form_type)";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $data['id']);
    $stmt->bindValue(':name', $data['name']);
    $stmt->bindValue(':description', $data['description']);
    $stmt->bindValue(':url', $data['url']);
    $stmt->bindValue(':image_url', $data['image_url']);
    $stmt->bindValue(':category', $data['category']);
    $stmt->bindValue(':is_active', $data['is_active']);
    $stmt->bindValue(':service_type', $data['service_type']);
    $stmt->bindValue(':parent_id', $data['parent_id'] ?: null);
    $stmt->bindValue(':form_type', $data['form_type']);

    return $stmt->execute();
}

function update_service($data) {
    $db = get_db_connection();
    $sql = "UPDATE services SET
            name = :name,
            description = :description,
            url = :url,
            category = :category,
            is_active = :is_active,
            service_type = :service_type,
            parent_id = :parent_id,
            form_type = :form_type" .
            ($data['image_url'] ? ", image_url = :image_url" : "") .
            " WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $data['id']);
    $stmt->bindValue(':name', $data['name']);
    $stmt->bindValue(':description', $data['description']);
    $stmt->bindValue(':url', $data['url']);
    $stmt->bindValue(':category', $data['category']);
    $stmt->bindValue(':is_active', $data['is_active']);
    $stmt->bindValue(':service_type', $data['service_type']);
    $stmt->bindValue(':parent_id', $data['parent_id'] ?: null);
    $stmt->bindValue(':form_type', $data['form_type']);
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
