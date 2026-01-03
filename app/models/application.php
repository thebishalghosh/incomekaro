<?php
function create_full_application($data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Insert into service_applications
        $sql = "INSERT INTO service_applications (id, white_label_id, partner_id, service_id, created_by, customer_name, customer_email, customer_phone, status)
                VALUES (:id, :white_label_id, :partner_id, :service_id, :created_by, :customer_name, :customer_email, :customer_phone, 'submitted')";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':white_label_id', $data['white_label_id']);
        $stmt->bindValue(':partner_id', $data['partner_id']);
        $stmt->bindValue(':service_id', $data['service_id']);
        $stmt->bindValue(':created_by', $data['created_by']);
        $stmt->bindValue(':customer_name', $data['customer']['first_name'] . ' ' . $data['customer']['last_name']);
        $stmt->bindValue(':customer_email', $data['customer']['email']);
        $stmt->bindValue(':customer_phone', $data['customer']['phone']);
        $stmt->execute();

        // 2. Insert Meta Data
        if (!empty($data['meta'])) {
            $sql = "INSERT INTO service_application_meta (id, application_id, field_key, field_value) VALUES (:id, :application_id, :field_key, :field_value)";
            $stmt = $db->prepare($sql);

            foreach ($data['meta'] as $key => $value) {
                $stmt->bindValue(':id', uniqid('meta-'));
                $stmt->bindValue(':application_id', $data['id']);
                $stmt->bindValue(':field_key', $key);
                $stmt->bindValue(':field_value', $value);
                $stmt->execute();
            }
        }

        // 3. Insert Documents
        if (!empty($data['documents'])) {
            $sql = "INSERT INTO documents (id, application_id, document_type, file_url, uploaded_by) VALUES (:id, :application_id, :document_type, :file_url, :uploaded_by)";
            $stmt = $db->prepare($sql);

            foreach ($data['documents'] as $doc) {
                $stmt->bindValue(':id', uniqid('doc-'));
                $stmt->bindValue(':application_id', $data['id']);
                $stmt->bindValue(':document_type', $doc['type']);
                $stmt->bindValue(':file_url', $doc['url']);
                $stmt->bindValue(':uploaded_by', $data['created_by']);
                $stmt->execute();
            }
        }

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        error_log($e->getMessage());
        return false;
    }
}

function get_partner_applications($partner_id) {
    $db = get_db_connection();
    $sql = "SELECT sa.*, s.name as service_name
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            WHERE sa.partner_id = :partner_id
            ORDER BY sa.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':partner_id', $partner_id);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_all_applications_for_admin() {
    $db = get_db_connection();
    $sql = "SELECT sa.*, s.name as service_name, p.name as partner_name, pp.full_name as partner_full_name
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            JOIN partners p ON sa.partner_id = p.id
            LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
            ORDER BY sa.created_at DESC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function get_application_by_id($id) {
    $db = get_db_connection();

    // 1. Get Main Application Data
    $sql = "SELECT sa.*, s.name as service_name, p.name as partner_name, pp.full_name as partner_full_name, pp.mobile as partner_phone
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            JOIN partners p ON sa.partner_id = p.id
            LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
            WHERE sa.id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $application = $stmt->fetch();

    if (!$application) return null;

    // 2. Get Meta Data
    $sql = "SELECT field_key, field_value FROM service_application_meta WHERE application_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    // Fetch as key-value pairs
    $application['meta'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // 3. Get Documents
    $sql = "SELECT * FROM documents WHERE application_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $application['documents'] = $stmt->fetchAll();

    return $application;
}

function update_application_status($id, $status) {
    $db = get_db_connection();
    $sql = "UPDATE service_applications SET status = :status WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':status', $status);
    $stmt->bindValue(':id', $id);
    return $stmt->execute();
}
