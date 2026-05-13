<?php
// White Label System
// This file is included very early, so we need to be careful with dependencies.

$WL_CONFIG = null;
$domain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? null;

if (!$domain && defined('APP_URL')) {
    $domain = parse_url(APP_URL, PHP_URL_HOST) ?: null;
}

if (!$domain) {
    $domain = 'localhost';
}

// Fix: Remove www. prefix if present to ensure matching with database
$domain = preg_replace('/^www\./i', '', $domain);

// Load all white label clients from a cached file or database
// For simplicity, let's assume a function get_all_wl_domains() exists
// This should be optimized in a real application (e.g., using a cache)
try {
    $db = get_db_connection();
    $stmt = $db->prepare("SELECT * FROM white_label_clients WHERE primary_domain = :domain OR id IN (SELECT white_label_id FROM white_label_domains WHERE domain = :domain)");
    $stmt->execute(['domain' => $domain]);
    $WL_CONFIG = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Database might not be ready yet, fail silently
    $WL_CONFIG = null;
}

if ($WL_CONFIG) {
    // Check Status
    if ($WL_CONFIG['status'] === 'inactive') {
        header('HTTP/1.1 503 Service Unavailable');
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Site Suspended</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                body {
                    background-color: #f8f9fa;
                    height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                }
                .suspended-card {
                    background: white;
                    padding: 3rem;
                    border-radius: 1rem;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
                    text-align: center;
                    max-width: 500px;
                    width: 90%;
                    border-top: 5px solid #dc3545;
                }
                .icon-wrapper {
                    width: 80px;
                    height: 80px;
                    background-color: #fde8e8;
                    color: #dc3545;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 1.5rem;
                    font-size: 2.5rem;
                }
                h1 {
                    font-weight: 700;
                    color: #343a40;
                    margin-bottom: 1rem;
                }
                p {
                    color: #6c757d;
                    margin-bottom: 2rem;
                    line-height: 1.6;
                }
                .btn-contact {
                    background-color: #343a40;
                    color: white;
                    padding: 0.75rem 2rem;
                    border-radius: 50px;
                    text-decoration: none;
                    font-weight: 600;
                    transition: all 0.3s;
                }
                .btn-contact:hover {
                    background-color: #212529;
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                    color: white;
                }
            </style>
        </head>
        <body>
            <div class="suspended-card">
                <div class="icon-wrapper">
                    <i class="fas fa-ban"></i>
                </div>
                <h1>Account Suspended</h1>
                <p>This platform is currently inactive. If you are the administrator, please contact support immediately to resolve this issue.</p>
                <a href="mailto:support@incomekaro.in" class="btn-contact">
                    <i class="fas fa-envelope me-2"></i> Contact Support
                </a>
                <div class="mt-4 text-muted small">
                    &copy; ' . date('Y') . ' IncomeKaro
                </div>
            </div>
        </body>
        </html>';
        exit;
    }

    define('IS_WHITE_LABEL', true);
} else {
    define('IS_WHITE_LABEL', false);
}

function get_site_name() {
    global $WL_CONFIG;
    if (IS_WHITE_LABEL && isset($WL_CONFIG['company_name'])) {
        return $WL_CONFIG['company_name'];
    }
    return SITE_NAME; // Default from config.php
}

function get_primary_color() {
    global $WL_CONFIG;
    if (IS_WHITE_LABEL && isset($WL_CONFIG['primary_color'])) {
        return $WL_CONFIG['primary_color'];
    }
    return '#6A5ACD'; // Default
}

function get_secondary_color() {
    global $WL_CONFIG;
    if (IS_WHITE_LABEL && isset($WL_CONFIG['secondary_color'])) {
        return $WL_CONFIG['secondary_color'];
    }
    return '#483D8B'; // Default
}

function get_logo_url() {
    global $WL_CONFIG;

    // Priority 1: Logged-in User's White Label
    if (isset($_SESSION['user_id'])) {
        // This is a potential performance hit on every page load. Caching user info in session is better.
        // Let's check if we already have the user's WL ID in session.
        // Assuming we don't, we fetch it.

        try {
            $db = get_db_connection();
            $stmt = $db->prepare("SELECT white_label_id FROM users WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $user_wl_id = $stmt->fetchColumn();

            if ($user_wl_id) {
                $stmt = $db->prepare("SELECT logo_url FROM white_label_clients WHERE id = :id");
                $stmt->execute(['id' => $user_wl_id]);
                $logo = $stmt->fetchColumn();
                if ($logo) {
                    return asset($logo);
                }
            }
        } catch (Exception $e) {
            // DB error, fall through
        }
    }

    // Priority 2: Domain-based White Label
    if (IS_WHITE_LABEL && !empty($WL_CONFIG['logo_url'])) {
        return asset($WL_CONFIG['logo_url']);
    }

    // Priority 3: Default Logo
    return asset('images/logo.png');
}

function get_favicon_url() {
    global $WL_CONFIG;

    // Priority 1: Logged-in User's White Label
    if (isset($_SESSION['user_id'])) {
        try {
            $db = get_db_connection();
            $stmt = $db->prepare("SELECT white_label_id FROM users WHERE id = :id");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $user_wl_id = $stmt->fetchColumn();

            if ($user_wl_id) {
                $stmt = $db->prepare("SELECT logo_url FROM white_label_clients WHERE id = :id");
                $stmt->execute(['id' => $user_wl_id]);
                $logo = $stmt->fetchColumn();
                if ($logo) {
                    return asset($logo); // Use logo as favicon
                }
            }
        } catch (Exception $e) {
            // DB error, fall through
        }
    }

    // Priority 2: Domain-based White Label
    if (IS_WHITE_LABEL && !empty($WL_CONFIG['logo_url'])) {
        return asset($WL_CONFIG['logo_url']);
    }

    // Priority 3: Default Favicon
    return asset('images/fav.png');
}
