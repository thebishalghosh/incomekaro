<?php
require_once APP_PATH . '/core/mailer.php'; // Include Mailer

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

            // Commit transaction BEFORE sending email
            // This ensures the user is created even if email fails
            $db->commit();

            // Send Welcome Email (Non-blocking)
            try {
                $subject = "Welcome to " . SITE_NAME . " - Your White Label Account";
                $login_url = URL_ROOT;

                $message = "
                    <h3>Welcome, {$data['company_name']}!</h3>
                    <p>Your White Label account has been successfully created.</p>
                    <p><strong>Login Details:</strong></p>
                    <ul>
                        <li><strong>Email:</strong> {$data['support_email']}</li>
                        <li><strong>Password:</strong> {$password}</li>
                    </ul>
                    <p><a href='{$login_url}'>Click here to Login</a></p>
                    <p>Please change your password after your first login.</p>
                    <br>
                    <p>Best Regards,<br>" . SITE_NAME . " Team</p>
                ";

                send_email($data['support_email'], $subject, $message);

                // Send Copy to Super Admin
                $sa_stmt = $db->prepare("SELECT email FROM users WHERE role_id = (SELECT id FROM roles WHERE code = 'SUPER_ADMIN') LIMIT 1");
                $sa_stmt->execute();
                $super_admin_email = $sa_stmt->fetchColumn();

                $sa_subject = "New White Label Created: " . $data['company_name'];
                $sa_message = "
                    <h3>New White Label Client Created</h3>
                    <p>A new white label client has been onboarded.</p>
                    <p><strong>Client Details:</strong></p>
                    <ul>
                        <li><strong>Company:</strong> {$data['company_name']}</li>
                        <li><strong>Domain:</strong> {$data['primary_domain']}</li>
                        <li><strong>Email:</strong> {$data['support_email']}</li>
                        <li><strong>Password:</strong> {$password}</li>
                    </ul>
                ";

                if ($super_admin_email) {
                    send_email($super_admin_email, $sa_subject, $sa_message);
                }

                // Send Copy to contact@incomekaro.org
                send_email('contact@incomekaro.org', $sa_subject, $sa_message);
            } catch (Exception $emailError) {
                // Log email error but don't fail the creation
                error_log("Email sending failed for WL creation: " . $emailError->getMessage());
            }

            return true; // Return true because DB commit was successful
        } else {
            // Role missing, rollback
            $db->rollBack();
            return false;
        }

    } catch (Exception $e) {
        // DB Error, rollback
        if ($db->inTransaction()) {
            $db->rollBack();
        }
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

function update_white_label_settings($id, $data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // Dynamic Update Query
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['company_name'])) { $fields[] = "company_name = :company_name"; $params[':company_name'] = $data['company_name']; }
        if (isset($data['logo_url'])) { $fields[] = "logo_url = :logo_url"; $params[':logo_url'] = $data['logo_url']; }
        if (isset($data['primary_color'])) { $fields[] = "primary_color = :primary_color"; $params[':primary_color'] = $data['primary_color']; }
        if (isset($data['secondary_color'])) { $fields[] = "secondary_color = :secondary_color"; $params[':secondary_color'] = $data['secondary_color']; }
        if (isset($data['landing_page_data'])) { $fields[] = "landing_page_data = :landing_page_data"; $params[':landing_page_data'] = $data['landing_page_data']; }

        // New Signature Fields
        if (isset($data['signatory_name'])) { $fields[] = "signatory_name = :signatory_name"; $params[':signatory_name'] = $data['signatory_name']; }
        if (isset($data['signatory_designation'])) { $fields[] = "signatory_designation = :signatory_designation"; $params[':signatory_designation'] = $data['signatory_designation']; }
        if (isset($data['signature_url'])) { $fields[] = "signature_url = :signature_url"; $params[':signature_url'] = $data['signature_url']; }

        if (empty($fields)) return true; // Nothing to update

        $sql = "UPDATE white_label_clients SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        // Sync User Name if company name changed
        if (isset($data['company_name'])) {
            $sql = "UPDATE users SET first_name = :first_name WHERE white_label_id = :id AND role_id = (SELECT id FROM roles WHERE code = 'WHITE_LABEL')";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':first_name', $data['company_name']);
            $stmt->bindValue(':id', $id);
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

function delete_white_label($id) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Delete Contact Inquiries
        $db->exec("DELETE FROM contact_inquiries WHERE white_label_id = '$id'");

        // 2. Delete White Label Settings & Domains & Subscriptions
        $db->exec("DELETE FROM white_label_settings WHERE white_label_id = '$id'");
        $db->exec("DELETE FROM white_label_domains WHERE white_label_id = '$id'");
        $db->exec("DELETE FROM white_label_services WHERE white_label_id = '$id'");
        $db->exec("DELETE FROM white_label_subscriptions WHERE white_label_id = '$id'");

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

// --- Subscription Helpers ---

function get_white_label_subscription($white_label_id) {
    $db = get_db_connection();
    $sql = "SELECT wls.*, sp.name as plan_name, sp.price as plan_price
            FROM white_label_subscriptions wls
            JOIN subscription_plans sp ON wls.plan_id = sp.id
            WHERE wls.white_label_id = :id AND wls.status = 'active'
            ORDER BY wls.created_at DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $white_label_id]);
    return $stmt->fetch();
}

function assign_white_label_subscription($data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // Deactivate existing active subscriptions
        $update = $db->prepare("UPDATE white_label_subscriptions SET status = 'expired' WHERE white_label_id = :id AND status = 'active'");
        $update->execute(['id' => $data['white_label_id']]);

        // Create new subscription
        $sql = "INSERT INTO white_label_subscriptions (id, white_label_id, plan_id, start_date, end_date, amount, due_amount, payment_status, status)
                VALUES (:id, :white_label_id, :plan_id, :start_date, :end_date, :amount, :due_amount, :payment_status, 'active')";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', uniqid('wls-'));
        $stmt->bindValue(':white_label_id', $data['white_label_id']);
        $stmt->bindValue(':plan_id', $data['plan_id']);
        $stmt->bindValue(':start_date', $data['start_date']);
        $stmt->bindValue(':end_date', $data['end_date']);
        $stmt->bindValue(':amount', $data['amount']);
        $stmt->bindValue(':due_amount', $data['due_amount'] ?? 0.00);
        $stmt->bindValue(':payment_status', $data['payment_status']);
        $stmt->execute();

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        error_log($e->getMessage());
        return false;
    }
}

function get_white_label_allowed_services($white_label_id) {
    $db = get_db_connection();

    // 1. Get Active Subscription
    $sub = get_white_label_subscription($white_label_id);

    if (!$sub) {
        return []; // No active subscription = No services
    }

    // 2. Get Services linked to the Plan
    $sql = "SELECT service_id FROM subscription_plan_services WHERE plan_id = :plan_id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['plan_id' => $sub['plan_id']]);
    $allowed_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return $allowed_ids;
}
