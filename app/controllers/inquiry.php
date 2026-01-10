<?php
require_once APP_PATH . '/core/database.php';
require_once APP_PATH . '/models/user.php'; // Need to fetch user details

function inquiry_index() {
    require_login();

    $db = get_db_connection();
    $inquiries = [];

    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        // Super Admin sees ALL inquiries
        $sql = "SELECT c.*, wl.company_name as wl_name
                FROM contact_inquiries c
                LEFT JOIN white_label_clients wl ON c.white_label_id = wl.id
                ORDER BY c.created_at DESC";
        $stmt = $db->query($sql);
        $inquiries = $stmt->fetchAll();

    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        // White Label Admin sees ONLY their inquiries
        $user = find_user_by_id($_SESSION['user_id']);
        if (empty($user['white_label_id'])) {
            die('Error: User not linked to White Label.');
        }

        $sql = "SELECT c.*, wl.company_name as wl_name
                FROM contact_inquiries c
                LEFT JOIN white_label_clients wl ON c.white_label_id = wl.id
                WHERE c.white_label_id = :wl_id
                ORDER BY c.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['wl_id' => $user['white_label_id']]);
        $inquiries = $stmt->fetchAll();

    } else {
        die('Access Denied');
    }

    view('dashboard/inquiries_list', ['inquiries' => $inquiries]);
}

function inquiry_view($id) {
    require_login();

    $db = get_db_connection();

    // Fetch inquiry first to check permissions
    $sql = "SELECT c.*, wl.company_name as wl_name
            FROM contact_inquiries c
            LEFT JOIN white_label_clients wl ON c.white_label_id = wl.id
            WHERE c.id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $id]);
    $inquiry = $stmt->fetch();

    if (!$inquiry) {
        flash('inq_error', 'Inquiry not found.');
        redirect('inquiry/index');
    }

    // Security Check
    if ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        if ($inquiry['white_label_id'] !== $user['white_label_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] !== 'SUPER_ADMIN') {
        die('Access Denied');
    }

    // Mark as read if new
    if ($inquiry['status'] == 'new') {
        $update = $db->prepare("UPDATE contact_inquiries SET status = 'read' WHERE id = :id");
        $update->execute(['id' => $id]);
        $inquiry['status'] = 'read'; // Update local variable for view
    }

    view('dashboard/inquiry_view', ['inquiry' => $inquiry]);
}

function inquiry_update_status($id) {
    require_login();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];
        $db = get_db_connection();

        // Security Check
        if ($_SESSION['role_code'] === 'WHITE_LABEL') {
            $user = find_user_by_id($_SESSION['user_id']);
            $check = $db->prepare("SELECT white_label_id FROM contact_inquiries WHERE id = :id");
            $check->execute(['id' => $id]);
            $inq = $check->fetch();

            if (!$inq || $inq['white_label_id'] !== $user['white_label_id']) {
                die('Access Denied');
            }
        } elseif ($_SESSION['role_code'] !== 'SUPER_ADMIN') {
            die('Access Denied');
        }

        $stmt = $db->prepare("UPDATE contact_inquiries SET status = :status WHERE id = :id");

        if ($stmt->execute(['status' => $status, 'id' => $id])) {
            flash('inq_success', 'Status updated successfully.');
        } else {
            flash('inq_error', 'Failed to update status.');
        }

        redirect('inquiry/index');
    }
}

function inquiry_delete($id) {
    require_login();

    $db = get_db_connection();

    // Security Check
    if ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        $check = $db->prepare("SELECT white_label_id FROM contact_inquiries WHERE id = :id");
        $check->execute(['id' => $id]);
        $inq = $check->fetch();

        if (!$inq || $inq['white_label_id'] !== $user['white_label_id']) {
            die('Access Denied');
        }
    } elseif ($_SESSION['role_code'] !== 'SUPER_ADMIN') {
        die('Access Denied');
    }

    $stmt = $db->prepare("DELETE FROM contact_inquiries WHERE id = :id");

    if ($stmt->execute(['id' => $id])) {
        flash('inq_success', 'Inquiry deleted.');
    } else {
        flash('inq_error', 'Failed to delete inquiry.');
    }

    redirect('inquiry/index');
}
