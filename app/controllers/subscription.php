<?php
require_once APP_PATH . '/models/subscription.php';
require_once APP_PATH . '/models/service.php';
require_once APP_PATH . '/models/white_label.php';
require_once APP_PATH . '/models/user.php';

function subscription_index() {
    require_login();

    if ($_SESSION['role_code'] === 'SUPER_ADMIN') {
        // Fetch all plans initially
        $partner_plans = get_all_subscription_plans('PARTNER');
        $wl_plans = get_all_subscription_plans('WHITE_LABEL');

        // Fetch all White Labels for the filter dropdown
        $white_labels = get_all_white_labels();

        view('dashboard/subscriptions_list', [
            'partner_plans' => $partner_plans,
            'wl_plans' => $wl_plans,
            'white_labels' => $white_labels
        ]);
    } elseif ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        $partner_plans = get_all_subscription_plans('PARTNER', $user['white_label_id']);

        view('dashboard/subscriptions_list', [
            'partner_plans' => $partner_plans,
            'wl_plans' => []
        ]);
    } else {
        die('Access Denied');
    }
}

function subscription_filter() {
    require_role('SUPER_ADMIN');

    $creator_id = $_GET['creator_id'] ?? 'all';

    // Determine filter logic
    $wl_id_filter = null;
    if ($creator_id === 'global') {
        $wl_id_filter = 'GLOBAL'; // Model handles this as IS NULL
    } elseif ($creator_id !== 'all') {
        $wl_id_filter = $creator_id;
    }

    $plans = get_all_subscription_plans('PARTNER', $wl_id_filter);

    // Return HTML rows
    if (empty($plans)) {
        echo '<tr><td colspan="5" class="text-center py-4 text-muted">No plans found for this selection.</td></tr>';
    } else {
        foreach ($plans as $plan) {
            $status_badge = $plan['status'] == 'active'
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';

            $edit_url = url('subscription/edit/' . $plan['id']);
            $delete_url = url('subscription/delete/' . $plan['id']);

            echo "<tr>
                <td class='ps-4 fw-bold'>{$plan['name']}</td>
                <td>₹" . number_format($plan['price'], 2) . "</td>
                <td>{$plan['gst_rate']}%</td>
                <td>{$status_badge}</td>
                <td class='pe-4 text-end'>
                    <a href='{$edit_url}' class='btn btn-sm btn-info text-white'><i class='fas fa-edit'></i></a>
                    <a href='{$delete_url}' class='btn btn-sm btn-danger' onclick=\"return confirm('Are you sure?');\"><i class='fas fa-trash'></i></a>
                </td>
            </tr>";
        }
    }
    exit; // Stop further execution
}

function subscription_create() {
    require_login();

    $services = get_top_level_services();

    if ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        $allowed_ids = get_white_label_allowed_services($user['white_label_id']);

        $services = array_filter($services, function($svc) use ($allowed_ids) {
            return in_array($svc['id'], $allowed_ids);
        });
    }

    view('forms/subscription_form', ['services' => $services]);
}

function subscription_store() {
    require_login();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $gst_rate = filter_input(INPUT_POST, 'gst_rate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $footer_description = filter_input(INPUT_POST, 'footer_description', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

        $white_label_id = null;

        if ($_SESSION['role_code'] === 'WHITE_LABEL') {
            $user = find_user_by_id($_SESSION['user_id']);
            $white_label_id = $user['white_label_id'];
            $type = 'PARTNER'; // Force type
        }

        $data = [
            'id' => uniqid('plan-'),
            'white_label_id' => $white_label_id,
            'name' => trim($name),
            'type' => $type,
            'price' => $price,
            'gst_rate' => $gst_rate,
            'description' => trim($description),
            'footer_description' => trim($footer_description),
            'status' => $status,
            'services' => $_POST['services'] ?? []
        ];

        if (create_subscription_plan($data)) {
            flash('sub_success', 'Subscription Plan Created');
            redirect('subscription/index');
        } else {
            flash('sub_error', 'Failed to create plan', 'alert alert-danger');
            redirect('subscription/create');
        }
    }
}

function subscription_edit($id) {
    require_login();

    $plan = get_subscription_plan_by_id($id);
    if (!$plan) {
        redirect('subscription/index');
    }

    // Security Check for WL Admin
    if ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        if ($plan['white_label_id'] !== $user['white_label_id']) {
            die('Access Denied');
        }
    }

    $services = get_top_level_services();

    if ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        $allowed_ids = get_white_label_allowed_services($user['white_label_id']);

        $services = array_filter($services, function($svc) use ($allowed_ids) {
            return in_array($svc['id'], $allowed_ids);
        });
    }

    view('forms/subscription_form', ['plan' => $plan, 'services' => $services]);
}

function subscription_update($id) {
    require_login();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $gst_rate = filter_input(INPUT_POST, 'gst_rate', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $footer_description = filter_input(INPUT_POST, 'footer_description', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

        if ($_SESSION['role_code'] === 'WHITE_LABEL') {
            $user = find_user_by_id($_SESSION['user_id']);
            // Verify ownership before update
            $current_plan = get_subscription_plan_by_id($id);
            if ($current_plan['white_label_id'] !== $user['white_label_id']) {
                die('Access Denied');
            }
            $type = 'PARTNER';
        }

        $data = [
            'id' => $id,
            'name' => trim($name),
            'type' => $type,
            'price' => $price,
            'gst_rate' => $gst_rate,
            'description' => trim($description),
            'footer_description' => trim($footer_description),
            'status' => $status,
            'services' => $_POST['services'] ?? []
        ];

        if (update_subscription_plan($data)) {
            flash('sub_success', 'Subscription Plan Updated');
            redirect('subscription/index');
        } else {
            flash('sub_error', 'Failed to update plan', 'alert alert-danger');
            redirect('subscription/edit/' . $id);
        }
    }
}

function subscription_delete($id) {
    require_login();

    if ($_SESSION['role_code'] === 'WHITE_LABEL') {
        $user = find_user_by_id($_SESSION['user_id']);
        $plan = get_subscription_plan_by_id($id);
        if ($plan['white_label_id'] !== $user['white_label_id']) {
            die('Access Denied');
        }
    }

    if (delete_subscription_plan($id)) {
        flash('sub_success', 'Subscription Plan Deleted');
    } else {
        flash('sub_error', 'Could not delete plan. It may have associated partners.', 'alert alert-danger');
    }
    redirect('subscription/index');
}
