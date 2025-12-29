<?php
require_once APP_PATH . '/models/service_application.php';

function application_index() {
    require_role('SUPER_ADMIN');
    $applications = get_all_applications();
    view('dashboard/applications_list', ['applications' => $applications]);
}

function application_view($id) {
    require_role('SUPER_ADMIN');
    $application = get_application_by_id($id);
    if (!$application) {
        redirect('application/index');
    }
    view('dashboard/application_details', ['application' => $application]);
}

function application_update_status($id) {
    require_role('SUPER_ADMIN');
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $status = $_POST['status'];
        if (update_application_status($id, $status)) {
            flash('app_success', 'Application status updated');
        } else {
            flash('app_error', 'Failed to update status', 'alert alert-danger');
        }
        redirect('application/index');
    }
}
