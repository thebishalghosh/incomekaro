<?php
function get_all_applications_for_admin() {
    $db = get_db_connection();
    $sql = "SELECT sa.*, s.name as service_name,
            COALESCE(pp.full_name, p.name) as partner_full_name,
            p.name as partner_name,
            wl.company_name as white_label_name
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            JOIN partners p ON sa.partner_id = p.id
            LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
            LEFT JOIN white_label_clients wl ON sa.white_label_id = wl.id
            ORDER BY sa.created_at DESC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function get_partner_applications($partner_id) {
    $db = get_db_connection();
    // Even for partners, we might want to show their own name or just avoid the error
    // But usually the view hides the partner column for partners.
    // Let's fetch it anyway for consistency.
    $sql = "SELECT sa.*, s.name as service_name,
            COALESCE(pp.full_name, p.name) as partner_full_name
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            JOIN partners p ON sa.partner_id = p.id
            LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
            WHERE sa.partner_id = :partner_id
            ORDER BY sa.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':partner_id', $partner_id);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_applications_by_white_label($white_label_id) {
    $db = get_db_connection();
    $sql = "SELECT sa.*, s.name as service_name,
            COALESCE(pp.full_name, p.name) as partner_full_name,
            p.id as partner_id
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            JOIN partners p ON sa.partner_id = p.id
            LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
            WHERE sa.white_label_id = :wl_id
            ORDER BY sa.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':wl_id', $white_label_id);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_partner_applications_by_service($partner_id, $service_id) {
    $db = get_db_connection();
    $sql = "SELECT sa.*, s.name as service_name,
            COALESCE(pp.full_name, p.name) as partner_full_name
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            JOIN partners p ON sa.partner_id = p.id
            LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
            WHERE sa.partner_id = :partner_id AND sa.service_id = :service_id
            ORDER BY sa.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':partner_id', $partner_id);
    $stmt->bindValue(':service_id', $service_id);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_application_by_id($id) {
    $db = get_db_connection();

    // Fetch main application data
    $sql = "SELECT sa.*, s.name as service_name, s.form_type,
            COALESCE(pp.full_name, p.name) as partner_name,
            p.id as partner_id,
            wl.white_label_id -- Fetch WL ID for color logic
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            JOIN partners p ON sa.partner_id = p.id
            LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
            LEFT JOIN white_label_clients wl ON sa.white_label_id = wl.id
            WHERE sa.id = :id";

    // Wait, the query above has an error: wl.white_label_id doesn't exist, it's wl.id or sa.white_label_id
    // Correcting:
    $sql = "SELECT sa.*, s.name as service_name, s.form_type,
            COALESCE(pp.full_name, p.name) as partner_name,
            p.id as partner_id,
            sa.white_label_id
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

    // Fetch meta data
    $sql = "SELECT field_key, field_value FROM service_application_meta WHERE application_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $meta_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $application['meta'] = $meta_data;

    // Fetch documents
    $sql = "SELECT * FROM documents WHERE application_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $application['documents'] = $stmt->fetchAll();

    // Fetch comments
    $sql = "SELECT c.*, u.first_name, u.last_name, u.profile_image, r.name as role_name
            FROM comments c
            JOIN users u ON c.user_id = u.id
            JOIN roles r ON u.role_id = r.id
            WHERE c.application_id = :id
            ORDER BY c.created_at ASC";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $application['comments'] = $stmt->fetchAll();

    return $application;
}

function create_full_application($data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Insert into service_applications
        $sql = "INSERT INTO service_applications (id, white_label_id, partner_id, service_id, created_by, customer_name, customer_email, customer_phone, status)
                VALUES (:id, :white_label_id, :partner_id, :service_id, :created_by, :customer_name, :customer_email, :customer_phone, :status)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':white_label_id', $data['white_label_id']);
        $stmt->bindValue(':partner_id', $data['partner_id']);
        $stmt->bindValue(':service_id', $data['service_id']);
        $stmt->bindValue(':created_by', $data['created_by']);
        $stmt->bindValue(':customer_name', $data['customer']['name']);
        $stmt->bindValue(':customer_email', $data['customer']['email']);
        $stmt->bindValue(':customer_phone', $data['customer']['phone']);
        $stmt->bindValue(':status', $data['status']);
        $stmt->execute();

        // 2. Insert meta data
        if (!empty($data['meta'])) {
            $sql = "INSERT INTO service_application_meta (id, application_id, field_key, field_value) VALUES (:id, :app_id, :key, :value)";
            $stmt = $db->prepare($sql);
            foreach ($data['meta'] as $key => $value) {
                $stmt->execute([
                    'id' => uniqid('meta-'),
                    'app_id' => $data['id'],
                    'key' => $key,
                    'value' => is_array($value) ? json_encode($value) : $value
                ]);
            }
        }

        // 3. Insert documents
        if (!empty($data['documents'])) {
            $sql = "INSERT INTO documents (id, application_id, document_type, file_url, uploaded_by) VALUES (:id, :app_id, :type, :url, :user_id)";
            $stmt = $db->prepare($sql);
            foreach ($data['documents'] as $doc) {
                $stmt->execute([
                    'id' => uniqid('doc-'),
                    'app_id' => $data['id'],
                    'type' => $doc['type'],
                    'url' => $doc['url'],
                    'user_id' => $data['created_by']
                ]);
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

function update_full_application($data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Update customer details
        $sql = "UPDATE service_applications SET customer_name = :name, customer_email = :email, customer_phone = :phone WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', $data['customer']['name']);
        $stmt->bindValue(':email', $data['customer']['email']);
        $stmt->bindValue(':phone', $data['customer']['phone']);
        $stmt->bindValue(':id', $data['id']);
        $stmt->execute();

        // 2. Update meta data (delete and re-insert)
        $db->exec("DELETE FROM service_application_meta WHERE application_id = '" . $data['id'] . "'");
        if (!empty($data['meta'])) {
            $sql = "INSERT INTO service_application_meta (id, application_id, field_key, field_value) VALUES (:id, :app_id, :key, :value)";
            $stmt = $db->prepare($sql);
            foreach ($data['meta'] as $key => $value) {
                $stmt->execute([
                    'id' => uniqid('meta-'),
                    'app_id' => $data['id'],
                    'key' => $key,
                    'value' => is_array($value) ? json_encode($value) : $value
                ]);
            }
        }

        // 3. Insert new documents
        if (!empty($data['documents'])) {
            $sql = "INSERT INTO documents (id, application_id, document_type, file_url, uploaded_by) VALUES (:id, :app_id, :type, :url, :user_id)";
            $stmt = $db->prepare($sql);
            foreach ($data['documents'] as $doc) {
                $stmt->execute([
                    'id' => uniqid('doc-'),
                    'app_id' => $data['id'],
                    'type' => $doc['type'],
                    'url' => $doc['url'],
                    'user_id' => $data['created_by']
                ]);
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

function update_application_status($id, $status) {
    $db = get_db_connection();
    $sql = "UPDATE service_applications SET status = :status WHERE id = :id";
    $stmt = $db->prepare($sql);
    return $stmt->execute(['status' => $status, 'id' => $id]);
}

function add_application_comment($id, $user_id, $comment) {
    $db = get_db_connection();
    $sql = "INSERT INTO comments (id, application_id, user_id, comment) VALUES (:id, :app_id, :user_id, :comment)";
    $stmt = $db->prepare($sql);
    return $stmt->execute([
        'id' => uniqid('com-'),
        'app_id' => $id,
        'user_id' => $user_id,
        'comment' => $comment
    ]);
}
