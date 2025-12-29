<?php
function route_request() {
    $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
    $url = filter_var($url, FILTER_SANITIZE_URL);
    $url_parts = explode('/', $url);

    // Default controller
    $controller_name = 'home';
    $action_name = 'index';
    $params = [];

    if (isset($url_parts[0]) && !empty($url_parts[0])) {
        $controller_name = $url_parts[0];
    }

    if (isset($url_parts[1]) && !empty($url_parts[1])) {
        $action_name = $url_parts[1];
    }

    if (count($url_parts) > 2) {
        $params = array_slice($url_parts, 2);
    }

    $controller_path = APP_PATH . '/controllers/' . $controller_name . '.php';

    if (file_exists($controller_path)) {
        require_once $controller_path;

        // Function name convention: controller_action (e.g., home_index, auth_login)
        // We replace hyphens with underscores for function names if needed
        $function_name = str_replace('-', '_', $controller_name) . '_' . str_replace('-', '_', $action_name);

        if (function_exists($function_name)) {
            call_user_func_array($function_name, $params);
        } else {
            // Fallback or 404
            die("Action '$action_name' not found in controller '$controller_name'.");
        }
    } else {
        // 404 Page
        die("Controller '$controller_name' not found.");
    }
}
