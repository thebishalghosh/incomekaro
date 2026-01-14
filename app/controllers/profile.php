<?php
require_once APP_PATH . '/models/user.php';

function profile_index() {
    require_login();
    $user = find_user_by_id($_SESSION['user_id']);
    view('dashboard/profile', ['user' => $user]);
}

function profile_update() {
    require_login();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user = find_user_by_id($_SESSION['user_id']);

        // Handle Profile Image
        $profile_image = $user['profile_image'];
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/profiles/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_dir . $file_name)) {
                $profile_image = 'uploads/profiles/' . $file_name;
            }
        }

        // Handle Bank Details
        $bank_details = [
            'account_holder_name' => $_POST['account_holder_name'],
            'bank_name' => $_POST['bank_name'],
            'account_number' => $_POST['account_number'],
            'ifsc_code' => $_POST['ifsc_code'],
            'branch' => $_POST['branch']
        ];

        $data = [
            'id' => $user['id'],
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $user['email'], // Email cannot be changed
            'phone' => $_POST['phone'],
            'status' => $user['status'],
            'role_id' => $user['role_id'],
            'monthly_target' => $user['monthly_target'] ?? 0,
            'bank_details' => $bank_details
        ];

        // We need to update the user model to handle profile_image update in update_user
        // Currently update_user doesn't handle profile_image.
        // Let's add a quick SQL update for image here or update the model.
        // Updating model is better.

        // For now, let's just update the basic info using existing function
        if (update_user($data)) {
            // Manually update profile image if changed
            if ($profile_image !== $user['profile_image']) {
                $db = get_db_connection();
                $stmt = $db->prepare("UPDATE users SET profile_image = :img WHERE id = :id");
                $stmt->execute(['img' => $profile_image, 'id' => $user['id']]);
            }

            flash('profile_success', 'Profile Updated Successfully');
        } else {
            flash('profile_error', 'Failed to update profile', 'alert alert-danger');
        }

        redirect('profile/index');
    }
}
