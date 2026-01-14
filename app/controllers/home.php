<?php
function home_index() {
    if (defined('IS_WHITE_LABEL') && IS_WHITE_LABEL) {
        // Load White Label Landing Page
        global $WL_CONFIG;

        // Decode landing page data
        $landing = !empty($WL_CONFIG['landing_page_data']) ? json_decode($WL_CONFIG['landing_page_data'], true) : [];

        view('white_label/landing', ['wl' => $WL_CONFIG, 'landing' => $landing]);
    } else {
        // Load Default Landing Page
        // Actually, for the main site, we usually have a public landing page too, not just dashboard/home.
        // But based on current code, it loads dashboard/home.
        // Let's keep it as is for main site, or redirect to a public view if exists.
        // Assuming 'home/index' is the public landing for main site.

        if (file_exists(APP_PATH . '/views/home/index.php')) {
            view('home/index');
        } else {
            $data = [
                'title' => 'Welcome to IncomeKaro',
                'message' => 'Your white-label financial platform is ready.'
            ];
            view('dashboard/home', $data);
        }
    }
}
