<?php
require_once APP_PATH . '/models/subscription.php'; // Required for dynamic pricing

function home_index() {
    if (defined('IS_WHITE_LABEL') && IS_WHITE_LABEL) {
        // Load White Label Landing Page
        global $WL_CONFIG;

        // Decode landing page data
        $landing = !empty($WL_CONFIG['landing_page_data']) ? json_decode($WL_CONFIG['landing_page_data'], true) : [];

        // Fetch White Label Specific Plans
        $plans = get_active_subscription_plans($WL_CONFIG['id'], 'PARTNER');

        view('white_label/landing', ['wl' => $WL_CONFIG, 'landing' => $landing, 'plans' => $plans]);
    } else {
        // Load Main Site Landing Page

        // Fetch Global Plans for Pricing Section (Only PARTNER plans)
        $plans = get_active_subscription_plans('GLOBAL', 'PARTNER');

        if (file_exists(APP_PATH . '/views/home/index.php')) {
            view('home/index', ['plans' => $plans]);
        } else {
            $data = [
                'title' => 'Welcome to IncomeKaro',
                'message' => 'Your white-label financial platform is ready.',
                'plans' => $plans
            ];
            view('dashboard/home', $data);
        }
    }
}
