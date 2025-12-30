<?php
require_once APP_PATH . '/models/user.php';

function user_index() {
    require_role('SUPER_ADMIN');
    $users = get_all_users();
    view('dashboard/users_list', ['users' => $users]);
}

function user_create() {
    require_role('SUPER_ADMIN');
    $roles = get_all_roles();
    view('forms/user_form', ['roles' => $roles]);
}

function user_store() {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $profile_image = '';
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/users/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = 'uploads/users/' . $file_name;
            }
        }

        $password_plain = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
        $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

        $data = [
            'id' => uniqid('u-'),
            'role_id' => trim($_POST['role_id']),
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
            'password_hash' => $password_hash,
            'profile_image' => $profile_image,
            'bank_details' => [
                'account_holder_name' => trim($_POST['account_holder_name']),
                'bank_name' => trim($_POST['bank_name']),
                'account_number' => trim($_POST['account_number']),
                'ifsc_code' => trim($_POST['ifsc_code']),
                'branch' => trim($_POST['bank_name']) // Branch is same as bank name in form
            ]
        ];

        if (create_full_user($data)) {
            $email_body = "<p>Hello <b>" . $data['first_name'] . "</b>,</p>";
            $email_body .= "<p>An account has been created for you on <b>" . SITE_NAME . "</b>.</p>";
            $email_body .= "<div class='info-box'>";
            $email_body .= "<h3 style='margin-top:0;'>Login Details</h3>";
            $email_body .= "<p><b>URL:</b> <a href='" . URL_ROOT . "'>" . URL_ROOT . "</a></p>";
            $email_body .= "<p><b>Email:</b> " . $data['email'] . "</p>";
            $email_body .= "<p><b>Password:</b> " . $password_plain . "</p>";
            $email_body .= "</div>";
            $email_body .= "<p>Please change your password after logging in.</p>";

            send_email($data['email'], 'Your Account on ' . SITE_NAME, $email_body);

            flash('user_success', 'User Created and Email Sent.');
            redirect('user/index');
        } else {
            flash('user_error', 'Failed to create user.', 'alert alert-danger');
            redirect('user/create');
        }
    }
}

function user_edit($id) {
    require_role('SUPER_ADMIN');
    $user = find_user_by_id($id);
    if (!$user) redirect('user/index');

    $roles = get_all_roles();
    view('forms/user_form', ['user' => $user, 'roles' => $roles]);
}

function user_update($id) {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $profile_image = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/users/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                $profile_image = 'uploads/users/' . $file_name;
            }
        }

        $data = [
            'id' => $id,
            'role_id' => trim($_POST['role_id']),
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
            'status' => trim($_POST['status']),
            'profile_image' => $profile_image,
            'bank_details' => [
                'account_holder_name' => trim($_POST['account_holder_name']),
                'bank_name' => trim($_POST['bank_name']),
                'account_number' => trim($_POST['account_number']),
                'ifsc_code' => trim($_POST['ifsc_code']),
                'branch' => trim($_POST['bank_name'])
            ]
        ];

        if (update_full_user($data)) {
            flash('user_success', 'User Updated Successfully.');
            redirect('user/index');
        } else {
            flash('user_error', 'Failed to update user.', 'alert alert-danger');
            redirect('user/edit/' . $id);
        }
    }
}

function user_delete($id) {
    require_role('SUPER_ADMIN');

    $user = find_user_by_id($id);
    if ($user && !empty($user['profile_image'])) {
        $file_path = APP_ROOT . '/public/' . $user['profile_image'];
        if (file_exists($file_path)) unlink($file_path);
    }

    if (delete_user($id)) {
        flash('user_success', 'User Deleted');
    } else {
        flash('user_error', 'Could not delete user.', 'alert alert-danger');
    }
    redirect('user/index');
}
