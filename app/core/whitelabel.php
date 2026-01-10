<?php
// White Label Logic

$host = $_SERVER['HTTP_HOST'];

// Strip port if present (e.g. localhost:8000 -> localhost)
if (strpos($host, ':') !== false) {
    $host = explode(':', $host)[0];
}

$default_host = 'incomekaro.test'; // Hardcoded main domain for safety, or fetch from env

// Global variable to store White Label Config
global $WL_CONFIG;
$WL_CONFIG = null;

// If the current host is NOT the default host, check for White Label
if ($host !== $default_host && $host !== 'localhost') {
    $db = get_db_connection();

    $stmt = $db->prepare("SELECT * FROM white_label_clients WHERE primary_domain = :domain AND status = 'active'");
    $stmt->execute(['domain' => $host]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client) {
        $WL_CONFIG = $client;

        // Decode JSON data
        if (!empty($client['landing_page_data'])) {
            $WL_CONFIG['landing_data'] = json_decode($client['landing_page_data'], true);
        }

        // Define a constant for easy checking
        define('IS_WHITE_LABEL', true);
    } else {
        // Domain not found in DB -> Redirect to Main Site
        // This prevents unregistered domains from showing the main site content
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        header("Location: " . $protocol . "://" . $default_host);
        exit;
    }
} else {
    define('IS_WHITE_LABEL', false);
}

// Helper function to get Site Name
function get_site_name() {
    global $WL_CONFIG;
    return $WL_CONFIG ? $WL_CONFIG['company_name'] : SITE_NAME;
}

// Helper function to get Logo URL
function get_logo_url() {
    global $WL_CONFIG;
    if ($WL_CONFIG && !empty($WL_CONFIG['logo_url'])) {
        return asset($WL_CONFIG['logo_url']);
    }
    return asset('images/logo.png');
}

// Helper function to get Primary Color
function get_primary_color() {
    global $WL_CONFIG;
    return $WL_CONFIG ? $WL_CONFIG['primary_color'] : '#667eea'; // Default purple
}

// Helper function to get Secondary Color
function get_secondary_color() {
    global $WL_CONFIG;
    return $WL_CONFIG ? $WL_CONFIG['secondary_color'] : '#764ba2'; // Default dark purple
}
