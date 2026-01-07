<?php
require_once APP_PATH . '/models/user.php';
require_once APP_PATH . '/core/mailer.php'; // Include Mailer

function user_index() {
    require_role('SUPER_ADMIN');
    $users = get_all_users();
    view('dashboard/users_list', ['users' => $users]);
}

function user_create() {
    require_role('SUPER_ADMIN');
    $all_roles = get_all_roles();
    // Filter out PARTNER_ADMIN role
    $roles = array_filter($all_roles, function($role) {
        return $role['code'] !== 'PARTNER_ADMIN';
    });
    view('forms/user_form', ['roles' => $roles]);
}

function user_store() {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $profile_image = '';
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/users/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = 'uploads/users/' . $file_name;
            }
        }

        $raw_password = $_POST['password']; // Keep raw password for email

        $data = [
            'id' => uniqid('u-'),
            'role_id' => $_POST['role_id'],
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
            'password_hash' => password_hash($raw_password, PASSWORD_DEFAULT),
            'profile_image' => $profile_image,
            'bank_details' => [
                'account_holder_name' => trim($_POST['account_holder_name']),
                'bank_name' => trim($_POST['bank_name']),
                'account_number' => trim($_POST['account_number']),
                'ifsc_code' => trim($_POST['ifsc_code']),
                'branch' => trim($_POST['branch'])
            ]
        ];

        // Check if email exists
        if (find_user_by_email($data['email'])) {
            flash('usr_error', 'Email already exists', 'alert alert-danger');
            redirect('user/create');
        }

        if (create_full_user($data)) {
            // Send Welcome Email
            send_welcome_email($data, $raw_password);

            flash('usr_success', 'User Created and Email Sent');
            redirect('user/index');
        } else {
            flash('usr_error', 'Failed to create user', 'alert alert-danger');
            redirect('user/create');
        }
    }
}

function user_edit($id) {
    require_role('SUPER_ADMIN');
    $user = find_user_by_id($id);
    if (!$user) {
        redirect('user/index');
    }
    $all_roles = get_all_roles();
    // Filter out PARTNER_ADMIN role
    $roles = array_filter($all_roles, function($role) {
        return $role['code'] !== 'PARTNER_ADMIN';
    });
    view('forms/user_form', ['user' => $user, 'roles' => $roles]);
}

function user_update($id) {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $profile_image = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/users/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = 'uploads/users/' . $file_name;
            }
        }

        $data = [
            'id' => $id,
            'role_id' => $_POST['role_id'],
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
            'status' => isset($_POST['is_active']) ? 'active' : 'inactive',
            'profile_image' => $profile_image,
            'bank_details' => [
                'account_holder_name' => trim($_POST['account_holder_name']),
                'bank_name' => trim($_POST['bank_name']),
                'account_number' => trim($_POST['account_number']),
                'ifsc_code' => trim($_POST['ifsc_code']),
                'branch' => trim($_POST['branch'])
            ]
        ];

        if (update_full_user($data)) {
            flash('usr_success', 'User Updated');
            redirect('user/index');
        } else {
            flash('usr_error', 'Failed to update user', 'alert alert-danger');
            redirect('user/edit/' . $id);
        }
    }
}

function user_delete($id) {
    require_role('SUPER_ADMIN');

    // First, get the user to delete image
    $user = find_user_by_id($id);
    if ($user && !empty($user['profile_image'])) {
        $file_path = APP_ROOT . '/public/' . $user['profile_image'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    if (delete_user($id)) {
        flash('usr_success', 'User Deleted');
    } else {
        flash('usr_error', 'Could not delete user.', 'alert alert-danger');
    }
    redirect('user/index');
}
