<?php
require_once APP_PATH . '/models/subscription.php';
require_once APP_PATH . '/models/service.php'; // To get services

function subscription_index() {
    require_role('SUPER_ADMIN');
    $plans = get_all_subscription_plans();
    view('dashboard/subscriptions_list', ['plans' => $plans]);
}

function subscription_create() {
    require_role('SUPER_ADMIN');
    $services = get_all_services(); // Fetch all master services
    view('forms/subscription_form', ['services' => $services]);
}

function subscription_store() {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data = [
            'id' => uniqid('plan-'),
            'name' => trim($_POST['name']),
            'price' => trim($_POST['price']),
            'gst_rate' => trim($_POST['gst_rate']),
            'description' => trim($_POST['description']),
            'footer_description' => trim($_POST['footer_description']),
            'status' => trim($_POST['status']),
            'services' => isset($_POST['services']) ? $_POST['services'] : []
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
    require_role('SUPER_ADMIN');
    $plan = get_subscription_plan_by_id($id);
    if (!$plan) {
        redirect('subscription/index');
    }
    $services = get_all_services();
    view('forms/subscription_form', ['plan' => $plan, 'services' => $services]);
}

function subscription_update($id) {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data = [
            'id' => $id,
            'name' => trim($_POST['name']),
            'price' => trim($_POST['price']),
            'gst_rate' => trim($_POST['gst_rate']),
            'description' => trim($_POST['description']),
            'footer_description' => trim($_POST['footer_description']),
            'status' => trim($_POST['status']),
            'services' => isset($_POST['services']) ? $_POST['services'] : []
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
    require_role('SUPER_ADMIN');
    if (delete_subscription_plan($id)) {
        flash('sub_success', 'Subscription Plan Deleted');
    } else {
        flash('sub_error', 'Could not delete plan.', 'alert alert-danger');
    }
    redirect('subscription/index');
}
