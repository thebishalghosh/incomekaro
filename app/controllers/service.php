<?php
require_once APP_PATH . '/models/service.php';

function service_index() {
    require_role('SUPER_ADMIN');
    // Only show top-level services in the main list to keep it clean
    $services = get_top_level_services();
    view('dashboard/services_list', ['services' => $services]);
}

function service_create() {
    require_role('SUPER_ADMIN');
    // Fetch only top-level services to be used as parents
    $parent_services = get_top_level_services();
    view('forms/service_form', ['all_services' => $parent_services]);
}

function service_store() {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $image_url = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/services/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = 'uploads/services/' . $file_name;
            }
        }

        $data = [
            'id' => uniqid('svc-'),
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'url' => trim($_POST['url']),
            'category' => trim($_POST['category']),
            'service_type' => trim($_POST['service_type']),
            'parent_id' => !empty($_POST['parent_id']) ? $_POST['parent_id'] : null,
            'form_type' => trim($_POST['form_type']),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'image_url' => $image_url
        ];

        if (create_service($data)) {
            flash('svc_success', 'Service Created');
            redirect('service/index');
        } else {
            flash('svc_error', 'Failed to create service', 'alert alert-danger');
            redirect('service/create');
        }
    }
}

function service_edit($id) {
    require_role('SUPER_ADMIN');
    $service = get_service_by_id($id);
    if (!$service) {
        redirect('service/index');
    }
    // Fetch only top-level services to be used as parents
    $parent_services = get_top_level_services();
    view('forms/service_form', ['service' => $service, 'all_services' => $parent_services]);
}

function service_update($id) {
    require_role('SUPER_ADMIN');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $upload_dir = APP_ROOT . '/public/uploads/services/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = 'uploads/services/' . $file_name;
            }
        }

        $data = [
            'id' => $id,
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'url' => trim($_POST['url']),
            'category' => trim($_POST['category']),
            'service_type' => trim($_POST['service_type']),
            'parent_id' => !empty($_POST['parent_id']) ? $_POST['parent_id'] : null,
            'form_type' => trim($_POST['form_type']),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'image_url' => $image_url
        ];

        if (update_service($data)) {
            flash('svc_success', 'Service Updated');
            redirect('service/index');
        } else {
            flash('svc_error', 'Failed to update service', 'alert alert-danger');
            redirect('service/edit/' . $id);
        }
    }
}

function service_delete($id) {
    require_role('SUPER_ADMIN');

    // First, get the service to delete its image
    $service = get_service_by_id($id);
    if ($service && !empty($service['image_url'])) {
        $file_path = APP_ROOT . '/public/' . $service['image_url'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    if (delete_service($id)) {
        flash('svc_success', 'Service Deleted');
    } else {
        flash('svc_error', 'Could not delete service.', 'alert alert-danger');
    }
    redirect('service/index');
}
