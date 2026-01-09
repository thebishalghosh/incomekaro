<?php
function home_index() {
    if (defined('IS_WHITE_LABEL') && IS_WHITE_LABEL) {
        // Load White Label Landing Page
        global $WL_CONFIG;
        view('white_label/landing', ['wl' => $WL_CONFIG]);
    } else {
        // Load Default Landing Page
        $data = [
            'title' => 'Welcome to IncomeKaro',
            'message' => 'Your white-label financial platform is ready.'
        ];
        view('dashboard/home', $data);
    }
}
