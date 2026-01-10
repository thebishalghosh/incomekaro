<?php
function get_all_white_labels() {
    $db = get_db_connection();
    $sql = "SELECT * FROM white_label_clients ORDER BY created_at DESC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function get_white_label_by_id($id) {
    $db = get_db_connection();
    $sql = "SELECT * FROM white_label_clients WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    return $stmt->fetch();
}

function create_white_label($data, $password = 'password123') {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Create Client
        $sql = "INSERT INTO white_label_clients (id, company_name, primary_domain, logo_url, primary_color, secondary_color, support_email, status)
                VALUES (:id, :company_name, :primary_domain, :logo_url, :primary_color, :secondary_color, :support_email, :status)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':company_name', $data['company_name']);
        $stmt->bindValue(':primary_domain', $data['primary_domain']);
        $stmt->bindValue(':logo_url', $data['logo_url']);
        $stmt->bindValue(':primary_color', $data['primary_color']);
        $stmt->bindValue(':secondary_color', $data['secondary_color']);
        $stmt->bindValue(':support_email', $data['support_email']);
        $stmt->bindValue(':status', $data['status']);
        $stmt->execute();

        // 2. Create User (Auto-generated)
        // Check if role exists
        $role_stmt = $db->prepare("SELECT id FROM roles WHERE code = 'WHITE_LABEL'");
        $role_stmt->execute();
        $role = $role_stmt->fetch();

        if ($role) {
            $user_id = uniqid('u-');
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (id, white_label_id, role_id, first_name, last_name, email, phone, password_hash, status)
                    VALUES (:id, :white_label_id, :role_id, :first_name, :last_name, :email, :phone, :password_hash, 'active')";

            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $user_id);
            $stmt->bindValue(':white_label_id', $data['id']);
            $stmt->bindValue(':role_id', $role['id']);
            $stmt->bindValue(':first_name', $data['company_name']);
            $stmt->bindValue(':last_name', 'Admin');
            $stmt->bindValue(':email', $data['support_email']);
            $stmt->bindValue(':phone', ''); // Optional
            $stmt->bindValue(':password_hash', $password_hash);
            $stmt->execute();
        }

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        error_log($e->getMessage());
        return false;
    }
}

function update_white_label($data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Update Client
        $sql = "UPDATE white_label_clients SET
                company_name = :company_name,
                primary_domain = :primary_domain,
                logo_url = :logo_url,
                primary_color = :primary_color,
                secondary_color = :secondary_color,
                support_email = :support_email,
                status = :status
                WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':company_name', $data['company_name']);
        $stmt->bindValue(':primary_domain', $data['primary_domain']);
        $stmt->bindValue(':logo_url', $data['logo_url']);
        $stmt->bindValue(':primary_color', $data['primary_color']);
        $stmt->bindValue(':secondary_color', $data['secondary_color']);
        $stmt->bindValue(':support_email', $data['support_email']);
        $stmt->bindValue(':status', $data['status']);
        $stmt->execute();

        // 2. Update Linked User Email AND Name
        // Find the user linked to this white label
        $sql = "UPDATE users SET email = :email, first_name = :first_name WHERE white_label_id = :id AND role_id = (SELECT id FROM roles WHERE code = 'WHITE_LABEL')";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $data['support_email']);
        $stmt->bindValue(':first_name', $data['company_name']);
        $stmt->bindValue(':id', $data['id']);
        $stmt->execute();

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        error_log($e->getMessage());
        return false;
    }
}

function update_white_label_settings($id, $company_name, $logo_url, $primary_color, $secondary_color, $landing_page_data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Update Client Settings
        $sql = "UPDATE white_label_clients SET
                company_name = :company_name,
                logo_url = :logo_url,
                primary_color = :primary_color,
                secondary_color = :secondary_color,
                landing_page_data = :landing_page_data
                WHERE id = :id";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':company_name', $company_name);
        $stmt->bindValue(':logo_url', $logo_url);
        $stmt->bindValue(':primary_color', $primary_color);
        $stmt->bindValue(':secondary_color', $secondary_color);
        $stmt->bindValue(':landing_page_data', $landing_page_data);
        $stmt->execute();

        // 2. Sync User Name (Optional but good for consistency)
        $sql = "UPDATE users SET first_name = :first_name WHERE white_label_id = :id AND role_id = (SELECT id FROM roles WHERE code = 'WHITE_LABEL')";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':first_name', $company_name);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        error_log($e->getMessage());
        return false;
    }
}

function delete_white_label($id) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Delete Contact Inquiries
        $db->exec("DELETE FROM contact_inquiries WHERE white_label_id = '$id'");

        // 2. Delete White Label Settings & Domains
        $db->exec("DELETE FROM white_label_settings WHERE white_label_id = '$id'");
        $db->exec("DELETE FROM white_label_domains WHERE white_label_id = '$id'");
        $db->exec("DELETE FROM white_label_services WHERE white_label_id = '$id'");

        // 3. Delete Applications & Related Data
        // First get all application IDs to delete child records
        $stmt = $db->query("SELECT id FROM service_applications WHERE white_label_id = '$id'");
        $app_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($app_ids)) {
            $ids_str = "'" . implode("','", $app_ids) . "'";
            $db->exec("DELETE FROM documents WHERE application_id IN ($ids_str)");
            $db->exec("DELETE FROM comments WHERE application_id IN ($ids_str)");
            $db->exec("DELETE FROM verification_logs WHERE application_id IN ($ids_str)");
            $db->exec("DELETE FROM service_application_meta WHERE application_id IN ($ids_str)");
            $db->exec("DELETE FROM service_applications WHERE white_label_id = '$id'");
        }

        // 4. Delete Partners & Related Data
        // First get all partner IDs
        $stmt = $db->query("SELECT id FROM partners WHERE white_label_id = '$id'");
        $partner_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($partner_ids)) {
            $ids_str = "'" . implode("','", $partner_ids) . "'";
            $db->exec("DELETE FROM partner_profiles WHERE partner_id IN ($ids_str)");
            $db->exec("DELETE FROM partner_addresses WHERE partner_id IN ($ids_str)");
            $db->exec("DELETE FROM partner_identity WHERE partner_id IN ($ids_str)");
            $db->exec("DELETE FROM partner_documents WHERE partner_id IN ($ids_str)");
            $db->exec("DELETE FROM partner_services WHERE partner_id IN ($ids_str)");
            $db->exec("DELETE FROM partner_subscriptions WHERE partner_id IN ($ids_str)");
            $db->exec("DELETE FROM partners WHERE white_label_id = '$id'");
        }

        // 5. Delete Users (Admins, RMs, Partners)
        // Note: We need to delete user_bank_details first
        $stmt = $db->query("SELECT id FROM users WHERE white_label_id = '$id'");
        $user_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($user_ids)) {
            $ids_str = "'" . implode("','", $user_ids) . "'";
            $db->exec("DELETE FROM user_bank_details WHERE user_id IN ($ids_str)");
            $db->exec("DELETE FROM withdrawals WHERE user_id IN ($ids_str)");
            $db->exec("DELETE FROM users WHERE white_label_id = '$id'");
        }

        // 6. Delete Client Record
        $sql = "DELETE FROM white_label_clients WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Delete WL Error: " . $e->getMessage());
        return false;
    }
}

function get_white_label_stats($white_label_id) {
    $db = get_db_connection();
    $stats = [];

    // 1. Total Partners
    $stmt = $db->prepare("SELECT COUNT(*) FROM partners WHERE white_label_id = :id");
    $stmt->execute(['id' => $white_label_id]);
    $stats['total_partners'] = $stmt->fetchColumn();

    // 2. Total Applications
    $stmt = $db->prepare("SELECT COUNT(*) FROM service_applications WHERE white_label_id = :id");
    $stmt->execute(['id' => $white_label_id]);
    $stats['total_applications'] = $stmt->fetchColumn();

    // 3. Pending Applications (Action Required)
    $stmt = $db->prepare("SELECT COUNT(*) FROM service_applications WHERE white_label_id = :id AND status IN ('submitted', 'under_verification')");
    $stmt->execute(['id' => $white_label_id]);
    $stats['pending_applications'] = $stmt->fetchColumn();

    // 4. Recent Applications
    $sql = "SELECT sa.*, s.name as service_name, p.name as partner_name
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            JOIN partners p ON sa.partner_id = p.id
            WHERE sa.white_label_id = :id
            ORDER BY sa.created_at DESC LIMIT 5";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $white_label_id]);
    $stats['recent_applications'] = $stmt->fetchAll();

    return $stats;
}
