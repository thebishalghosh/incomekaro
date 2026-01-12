<?php
function get_platform_partners() {
    $db = get_db_connection();
    $sql = "SELECT p.*, pp.full_name, pp.mobile, pp.email, pp.profile_image,
            u.id as user_id, u.wallet_balance
            FROM partners p
            LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
            LEFT JOIN users u ON u.partner_id = p.id
            WHERE p.partner_type = 'PLATFORM'
            ORDER BY p.created_at DESC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function get_all_partners_for_admin() {
    $db = get_db_connection();
    $sql = "SELECT p.*, pp.full_name, pp.mobile, pp.email, pp.profile_image, wl.company_name as white_label_name,
            u.id as user_id, u.wallet_balance
            FROM partners p
            LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
            LEFT JOIN white_label_clients wl ON p.white_label_id = wl.id
            LEFT JOIN users u ON u.partner_id = p.id
            ORDER BY p.created_at DESC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll();
}

function get_partners_by_rm($rm_id) {
    $db = get_db_connection();
    $sql = "SELECT p.*, pp.full_name, pp.mobile, pp.email, pp.profile_image, wl.company_name as white_label_name,
            u.id as user_id, u.wallet_balance
            FROM partners p
            LEFT JOIN partner_profiles pp ON p.id = pp.partner_id
            LEFT JOIN white_label_clients wl ON p.white_label_id = wl.id
            LEFT JOIN users u ON u.partner_id = p.id
            WHERE p.rm_id = :rm_id
            ORDER BY p.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_partner_by_id($id) {
    $db = get_db_connection();

    // Fetch main partner data with creator and RM info
    $sql = "SELECT p.*,
            creator.first_name as creator_first, creator.last_name as creator_last, creator.email as creator_email, creator.phone as creator_phone, r_creator.code as creator_role,
            rm.first_name as rm_first, rm.last_name as rm_last, rm.email as rm_email, rm.phone as rm_phone
            FROM partners p
            LEFT JOIN users creator ON p.created_by = creator.id
            LEFT JOIN roles r_creator ON creator.role_id = r_creator.id
            LEFT JOIN users rm ON p.rm_id = rm.id
            WHERE p.id = :id";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $partner = $stmt->fetch();

    if (!$partner) return null;

    // Fetch Profile
    $sql = "SELECT * FROM partner_profiles WHERE partner_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $partner['profile'] = $stmt->fetch();

    // Fetch Addresses
    $sql = "SELECT * FROM partner_addresses WHERE partner_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $addresses = $stmt->fetchAll();

    $partner['address_permanent'] = null;
    $partner['address_office'] = null;

    foreach ($addresses as $addr) {
        if ($addr['type'] == 'permanent') $partner['address_permanent'] = $addr;
        if ($addr['type'] == 'office') $partner['address_office'] = $addr;
    }

    // Fetch Identity
    $sql = "SELECT * FROM partner_identity WHERE partner_id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $partner['identity'] = $stmt->fetch();

    // Fetch Subscription
    $sql = "SELECT * FROM partner_subscriptions WHERE partner_id = :id ORDER BY created_at DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $partner['subscription'] = $stmt->fetch();

    // Fetch Plan Details & Services if subscription exists
    if ($partner['subscription']) {
        // FIX: Filter plan by Partner's White Label ID to avoid name collisions
        $sql = "SELECT * FROM subscription_plans
                WHERE name = :name
                AND (white_label_id = :wl_id OR white_label_id IS NULL)
                ORDER BY white_label_id DESC -- Prefer specific WL plan over Global
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':name', $partner['subscription']['plan_name']);
        $stmt->bindValue(':wl_id', $partner['white_label_id']);
        $stmt->execute();
        $plan_details = $stmt->fetch();

        if ($plan_details) {
            $partner['subscription']['gst_rate'] = $plan_details['gst_rate'];
            $partner['subscription']['base_price'] = $plan_details['price'];

            // Fetch Full Service Details
            $sql = "SELECT s.* FROM services s
                    JOIN subscription_plan_services sps ON s.id = sps.service_id
                    WHERE sps.plan_id = :plan_id AND s.is_active = 1
                    ORDER BY s.category, s.name";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':plan_id', $plan_details['id']);
            $stmt->execute();
            $partner['subscription']['services'] = $stmt->fetchAll();
        } else {
            $partner['subscription']['services'] = [];
        }
    }

    // Fetch Bank Details (via User)
    $sql = "SELECT u.id FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.partner_id = :partner_id AND r.code = 'PARTNER_ADMIN'
            LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':partner_id', $id);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        $sql = "SELECT * FROM user_bank_details WHERE user_id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':user_id', $user['id']);
        $stmt->execute();
        $partner['bank_details'] = $stmt->fetch();
    } else {
        $partner['bank_details'] = null;
    }

    // Fetch KYC Documents
    $partner['documents'] = get_partner_documents($id);

    return $partner;
}

function get_partner_stats($partner_id) {
    $db = get_db_connection();
    $stats = [];

    // 1. Applications by Status (for chart)
    $sql = "SELECT status, COUNT(*) as count FROM service_applications WHERE partner_id = :partner_id GROUP BY status";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':partner_id', $partner_id);
    $stmt->execute();
    $stats['by_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // 2. Applications by Service Category (for stat cards)
    $sql = "SELECT s.category, COUNT(*) as count
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            WHERE sa.partner_id = :partner_id
            GROUP BY s.category";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':partner_id', $partner_id);
    $stmt->execute();
    $stats['by_category'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // 3. Applications by Root Service Name (for chart)
    // This query traces back up the parent_id chain to find the top-level service name
    $sql = "SELECT COALESCE(p2.name, p1.name, s.name) as root_name, COUNT(sa.id) as count
            FROM service_applications sa
            JOIN services s ON sa.service_id = s.id
            LEFT JOIN services p1 ON s.parent_id = p1.id
            LEFT JOIN services p2 ON p1.parent_id = p2.id
            WHERE sa.partner_id = :partner_id
            GROUP BY root_name";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':partner_id', $partner_id);
    $stmt->execute();
    $stats['by_root_service'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // 4. Summary Counts
    $stats['total_applications'] = array_sum($stats['by_status']);
    $stats['total_approved'] = $stats['by_status']['APPROVED'] ?? 0;
    $stats['total_rejected'] = $stats['by_status']['REJECT'] ?? 0;

    $pending_statuses = ['FRESH', 'DOCUMENTS_UPLOAD', 'HOLD', 'LOGIN', 'DOCUMENTS_PENDING', 'BANKAR_PENDENCY'];
    $stats['total_pending'] = 0;
    foreach ($pending_statuses as $status) {
        $stats['total_pending'] += $stats['by_status'][$status] ?? 0;
    }

    return $stats;
}

function accept_agreement($partner_id) {
    $db = get_db_connection();
    $sql = "UPDATE partners SET agreement_accepted_at = CURRENT_TIMESTAMP WHERE id = :partner_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':partner_id', $partner_id);
    return $stmt->execute();
}

function get_partner_documents($partner_id) {
    $db = get_db_connection();
    $sql = "SELECT * FROM partner_documents WHERE partner_id = :partner_id ORDER BY uploaded_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':partner_id', $partner_id);
    $stmt->execute();
    return $stmt->fetchAll();
}

function add_partner_document($partner_id, $doc_type, $file_url) {
    $db = get_db_connection();
    // Overwrite existing doc of the same type
    $db->exec("DELETE FROM partner_documents WHERE partner_id = '$partner_id' AND document_type = '$doc_type'");

    $sql = "INSERT INTO partner_documents (id, partner_id, document_type, file_url) VALUES (:id, :partner_id, :doc_type, :file_url)";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', uniqid('doc-'));
    $stmt->bindValue(':partner_id', $partner_id);
    $stmt->bindValue(':doc_type', $doc_type);
    $stmt->bindValue(':file_url', $file_url);
    return $stmt->execute();
}

function update_kyc_status($partner_id, $status) {
    $db = get_db_connection();
    $sql = "UPDATE partners SET kyc_status = :status WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':status', $status);
    $stmt->bindValue(':id', $partner_id);
    return $stmt->execute();
}

function update_partner_status($partner_id, $status) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // Update Partner Status
        $sql = "UPDATE partners SET status = :status WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $partner_id);
        $stmt->execute();

        // Update Linked User Status
        $sql = "UPDATE users SET status = :status WHERE partner_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $partner_id);
        $stmt->execute();

        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        return false;
    }
}

function assign_rm_to_partner($partner_id, $rm_id) {
    $db = get_db_connection();
    $sql = "UPDATE partners SET rm_id = :rm_id WHERE id = :partner_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':rm_id', $rm_id);
    $stmt->bindValue(':partner_id', $partner_id);
    return $stmt->execute();
}

function create_full_partner($data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Insert into partners (Added created_by)
        $sql = "INSERT INTO partners (id, white_label_id, partner_type, name, email, phone, status, created_by)
                VALUES (:id, :white_label_id, :partner_type, :name, :email, :phone, :status, :created_by)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':white_label_id', $data['white_label_id']);
        $stmt->bindValue(':partner_type', $data['partner_type']);
        $stmt->bindValue(':name', $data['profile']['full_name']);
        $stmt->bindValue(':email', $data['profile']['email']);
        $stmt->bindValue(':phone', $data['profile']['mobile']);
        $stmt->bindValue(':status', $data['status']);
        $stmt->bindValue(':created_by', $data['created_by']); // New Field
        $stmt->execute();

        // 2. Insert into partner_profiles
        $sql = "INSERT INTO partner_profiles (id, partner_id, profile_image, full_name, mobile, email, whatsapp, dob, gender)
                VALUES (:id, :partner_id, :profile_image, :full_name, :mobile, :email, :whatsapp, :dob, :gender)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', uniqid('pp-'));
        $stmt->bindValue(':partner_id', $data['id']);
        $stmt->bindValue(':profile_image', $data['profile']['profile_image']);
        $stmt->bindValue(':full_name', $data['profile']['full_name']);
        $stmt->bindValue(':mobile', $data['profile']['mobile']);
        $stmt->bindValue(':email', $data['profile']['email']);
        $stmt->bindValue(':whatsapp', $data['profile']['whatsapp']);
        $stmt->bindValue(':dob', $data['profile']['dob'] ?: null);
        $stmt->bindValue(':gender', $data['profile']['gender']);
        $stmt->execute();

        // 3. Insert Permanent Address
        if (!empty($data['address_permanent'])) {
            $sql = "INSERT INTO partner_addresses (id, partner_id, type, address, state, city, pincode)
                    VALUES (:id, :partner_id, 'permanent', :address, :state, :city, :pincode)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', uniqid('pa-'));
            $stmt->bindValue(':partner_id', $data['id']);
            $stmt->bindValue(':address', $data['address_permanent']['address']);
            $stmt->bindValue(':state', $data['address_permanent']['state']);
            $stmt->bindValue(':city', $data['address_permanent']['city']);
            $stmt->bindValue(':pincode', $data['address_permanent']['pincode']);
            $stmt->execute();
        }

        // 4. Insert Office Address
        if (!empty($data['address_office'])) {
            $sql = "INSERT INTO partner_addresses (id, partner_id, type, address, state, city, pincode)
                    VALUES (:id, :partner_id, 'office', :address, :state, :city, :pincode)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', uniqid('pa-'));
            $stmt->bindValue(':partner_id', $data['id']);
            $stmt->bindValue(':address', $data['address_office']['address']);
            $stmt->bindValue(':state', $data['address_office']['state']);
            $stmt->bindValue(':city', $data['address_office']['city']);
            $stmt->bindValue(':pincode', $data['address_office']['pincode']);
            $stmt->execute();
        }

        // 5. Insert Identity
        if (!empty($data['identity'])) {
            $sql = "INSERT INTO partner_identity (id, partner_id, gst, aadhaar, pan)
                    VALUES (:id, :partner_id, :gst, :aadhaar, :pan)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', uniqid('pi-'));
            $stmt->bindValue(':partner_id', $data['id']);
            $stmt->bindValue(':gst', $data['identity']['gst']);
            $stmt->bindValue(':aadhaar', $data['identity']['aadhaar']);
            $stmt->bindValue(':pan', $data['identity']['pan']);
            $stmt->execute();
        }

        // 6. Insert Subscription
        if (!empty($data['subscription'])) {
            $sql = "INSERT INTO partner_subscriptions (id, partner_id, plan_name, payment_amount, due_amount, payment_mode, transaction_id, status)
                    VALUES (:id, :partner_id, :plan_name, :payment_amount, :due_amount, :payment_mode, :transaction_id, 'active')";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', uniqid('ps-'));
            $stmt->bindValue(':partner_id', $data['id']);
            $stmt->bindValue(':plan_name', $data['subscription']['plan_name']);
            $stmt->bindValue(':payment_amount', $data['subscription']['payment_amount']);
            $stmt->bindValue(':due_amount', $data['subscription']['due_amount']);
            $stmt->bindValue(':payment_mode', $data['subscription']['payment_mode']);
            $stmt->bindValue(':transaction_id', $data['subscription']['transaction_id']);
            $stmt->execute();
        }

        // 7. Auto-Create User
        $role_stmt = $db->prepare("SELECT id FROM roles WHERE code = 'PARTNER_ADMIN'");
        $role_stmt->execute();
        $role = $role_stmt->fetch();

        if (!$role) {
            $db->exec("INSERT INTO roles (code, name) VALUES ('PARTNER_ADMIN', 'Partner Admin')");
            $role_id = $db->lastInsertId();
        } else {
            $role_id = $role['id'];
        }

        $user_id = uniqid('u-');
        $sql = "INSERT INTO users (id, white_label_id, partner_id, role_id, first_name, last_name, email, phone, password_hash, status)
                VALUES (:id, :white_label_id, :partner_id, :role_id, :first_name, :last_name, :email, :phone, :password_hash, :status)";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $user_id);
        $stmt->bindValue(':white_label_id', $data['white_label_id']);
        $stmt->bindValue(':partner_id', $data['id']);
        $stmt->bindValue(':role_id', $role_id);

        $parts = explode(' ', $data['profile']['full_name'], 2);
        $first_name = $parts[0];
        $last_name = isset($parts[1]) ? $parts[1] : '';

        $stmt->bindValue(':first_name', $first_name);
        $stmt->bindValue(':last_name', $last_name);
        $stmt->bindValue(':email', $data['profile']['email']);
        $stmt->bindValue(':phone', $data['profile']['mobile']);
        $stmt->bindValue(':password_hash', $data['user_password_hash']);
        $stmt->bindValue(':status', 'active');
        $stmt->execute();

        // 8. Insert Bank Details (Linked to User)
        if (!empty($data['bank_details'])) {
            $sql = "INSERT INTO user_bank_details (id, user_id, account_holder_name, bank_name, account_number, ifsc_code, branch)
                    VALUES (:id, :user_id, :account_holder_name, :bank_name, :account_number, :ifsc_code, :branch)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', uniqid('ub-'));
            $stmt->bindValue(':user_id', $user_id);
            $stmt->bindValue(':account_holder_name', $data['bank_details']['account_holder_name']);
            $stmt->bindValue(':bank_name', $data['bank_details']['bank_name']);
            $stmt->bindValue(':account_number', $data['bank_details']['account_number']);
            $stmt->bindValue(':ifsc_code', $data['bank_details']['ifsc_code']);
            $stmt->bindValue(':branch', $data['bank_details']['branch']);
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

function update_full_partner($data) {
    $db = get_db_connection();

    try {
        $db->beginTransaction();

        // 1. Update partners
        $sql = "UPDATE partners SET status = :status WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':status', $data['status']);
        $stmt->bindValue(':id', $data['id']);
        $stmt->execute();

        // 2. Update partner_profiles
        $sql = "UPDATE partner_profiles SET
                full_name = :full_name,
                mobile = :mobile,
                email = :email,
                whatsapp = :whatsapp,
                dob = :dob,
                gender = :gender
                WHERE partner_id = :id";

        if (!empty($data['profile']['profile_image'])) {
            $sql = "UPDATE partner_profiles SET
                    full_name = :full_name,
                    mobile = :mobile,
                    email = :email,
                    whatsapp = :whatsapp,
                    dob = :dob,
                    gender = :gender,
                    profile_image = :profile_image
                    WHERE partner_id = :id";
        }

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':full_name', $data['profile']['full_name']);
        $stmt->bindValue(':mobile', $data['profile']['mobile']);
        $stmt->bindValue(':email', $data['profile']['email']);
        $stmt->bindValue(':whatsapp', $data['profile']['whatsapp']);
        $stmt->bindValue(':dob', $data['profile']['dob'] ?: null);
        $stmt->bindValue(':gender', $data['profile']['gender']);
        $stmt->bindValue(':id', $data['id']);

        if (!empty($data['profile']['profile_image'])) {
            $stmt->bindValue(':profile_image', $data['profile']['profile_image']);
        }
        $stmt->execute();

        // 3. Update Addresses (Delete and Re-insert is easier, or Update if exists)
        // Let's use Delete/Insert for simplicity or Update if ID known.
        // Since we don't pass address IDs, let's update by type.

        // Permanent
        $sql = "UPDATE partner_addresses SET address = :address, state = :state, city = :city, pincode = :pincode WHERE partner_id = :id AND type = 'permanent'";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':address', $data['address_permanent']['address']);
        $stmt->bindValue(':state', $data['address_permanent']['state']);
        $stmt->bindValue(':city', $data['address_permanent']['city']);
        $stmt->bindValue(':pincode', $data['address_permanent']['pincode']);
        $stmt->bindValue(':id', $data['id']);
        $stmt->execute();
        if ($stmt->rowCount() == 0) { // If not exists, insert
             $sql = "INSERT INTO partner_addresses (id, partner_id, type, address, state, city, pincode) VALUES (:id, :pid, 'permanent', :addr, :st, :ct, :pin)";
             $stmt = $db->prepare($sql);
             $stmt->execute(['id'=>uniqid('pa-'), 'pid'=>$data['id'], 'addr'=>$data['address_permanent']['address'], 'st'=>$data['address_permanent']['state'], 'ct'=>$data['address_permanent']['city'], 'pin'=>$data['address_permanent']['pincode']]);
        }

        // Office
        $sql = "UPDATE partner_addresses SET address = :address, state = :state, city = :city, pincode = :pincode WHERE partner_id = :id AND type = 'office'";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':address', $data['address_office']['address']);
        $stmt->bindValue(':state', $data['address_office']['state']);
        $stmt->bindValue(':city', $data['address_office']['city']);
        $stmt->bindValue(':pincode', $data['address_office']['pincode']);
        $stmt->bindValue(':id', $data['id']);
        $stmt->execute();
        if ($stmt->rowCount() == 0 && !empty($data['address_office']['address'])) {
             $sql = "INSERT INTO partner_addresses (id, partner_id, type, address, state, city, pincode) VALUES (:id, :pid, 'office', :addr, :st, :ct, :pin)";
             $stmt = $db->prepare($sql);
             $stmt->execute(['id'=>uniqid('pa-'), 'pid'=>$data['id'], 'addr'=>$data['address_office']['address'], 'st'=>$data['address_office']['state'], 'ct'=>$data['address_office']['city'], 'pin'=>$data['address_office']['pincode']]);
        }

        // 4. Update Identity
        $sql = "UPDATE partner_identity SET gst = :gst, aadhaar = :aadhaar, pan = :pan WHERE partner_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':gst', $data['identity']['gst']);
        $stmt->bindValue(':aadhaar', $data['identity']['aadhaar']);
        $stmt->bindValue(':pan', $data['identity']['pan']);
        $stmt->bindValue(':id', $data['id']);
        $stmt->execute();

        // 5. Update Subscription (Only payment details, plan change logic is complex)
        // For now, just update payment details of the latest subscription
        $sql = "UPDATE partner_subscriptions SET payment_amount = :amt, due_amount = :due, payment_mode = :mode, transaction_id = :tid WHERE partner_id = :id ORDER BY created_at DESC LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':amt', $data['subscription']['payment_amount']);
        $stmt->bindValue(':due', $data['subscription']['due_amount']);
        $stmt->bindValue(':mode', $data['subscription']['payment_mode']);
        $stmt->bindValue(':tid', $data['subscription']['transaction_id']);
        $stmt->bindValue(':id', $data['id']);
        $stmt->execute();

        // 6. Update Bank Details (via User)
        $user = $db->query("SELECT id FROM users WHERE partner_id = '" . $data['id'] . "'")->fetch();
        if ($user) {
            $sql = "UPDATE user_bank_details SET account_holder_name = :holder, bank_name = :bank, account_number = :acc, ifsc_code = :ifsc, branch = :branch WHERE user_id = :uid";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':holder', $data['bank_details']['account_holder_name']);
            $stmt->bindValue(':bank', $data['bank_details']['bank_name']);
            $stmt->bindValue(':acc', $data['bank_details']['account_number']);
            $stmt->bindValue(':ifsc', $data['bank_details']['ifsc_code']);
            $stmt->bindValue(':branch', $data['bank_details']['branch']);
            $stmt->bindValue(':uid', $user['id']);
            $stmt->execute();

            if ($stmt->rowCount() == 0) { // Insert if missing
                $sql = "INSERT INTO user_bank_details (id, user_id, account_holder_name, bank_name, account_number, ifsc_code, branch) VALUES (:id, :uid, :holder, :bank, :acc, :ifsc, :branch)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'id' => uniqid('ub-'),
                    'uid' => $user['id'],
                    'holder' => $data['bank_details']['account_holder_name'],
                    'bank' => $data['bank_details']['bank_name'],
                    'acc' => $data['bank_details']['account_number'],
                    'ifsc' => $data['bank_details']['ifsc_code'],
                    'branch' => $data['bank_details']['branch']
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

function delete_partner($id) {
    $db = get_db_connection();

    // 1. Get Profile Image Path
    $stmt = $db->prepare("SELECT profile_image FROM partner_profiles WHERE partner_id = :id");
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $profile = $stmt->fetch();

    // Delete File
    if ($profile && !empty($profile['profile_image'])) {
        $file_path = APP_ROOT . '/public/' . $profile['profile_image'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // 2. Delete related data
    $db->exec("DELETE FROM partner_profiles WHERE partner_id = '$id'");
    $db->exec("DELETE FROM partner_addresses WHERE partner_id = '$id'");
    $db->exec("DELETE FROM partner_identity WHERE partner_id = '$id'");
    $db->exec("DELETE FROM partner_services WHERE partner_id = '$id'");
    $db->exec("DELETE FROM partner_subscriptions WHERE partner_id = '$id'");

    // Delete bank details before deleting user
    $user = $db->query("SELECT id FROM users WHERE partner_id = '$id'")->fetch();
    if ($user) {
        $db->exec("DELETE FROM user_bank_details WHERE user_id = '" . $user['id'] . "'");
    }

    $db->exec("DELETE FROM users WHERE partner_id = '$id'");

    $sql = "DELETE FROM partners WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id);
    return $stmt->execute();
}
