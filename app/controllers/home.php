<?php
function home_index() {
    $data = [
        'title' => 'Welcome to IncomeKaro',
        'message' => 'Your white-label financial platform is ready.'
    ];
    view('dashboard/home', $data);
}
