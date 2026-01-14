<?php
// White Label System
// This file is included very early, so we need to be careful with dependencies.

$WL_CONFIG = null;
$domain = $_SERVER['HTTP_HOST'];

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
