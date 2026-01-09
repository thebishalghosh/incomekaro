<?php
// Simple Domain Detection Test

$host = $_SERVER['HTTP_HOST'];

echo "<h1>Domain Detection Test</h1>";
echo "<p>Current Host: <strong>" . $host . "</strong></p>";

echo "<hr>";

if ($host === 'incomekaro.test') {
    echo "<h2 style='color: blue;'>This is the MAIN SITE (Super Admin)</h2>";
} elseif ($host === 'partner1.test') {
    echo "<h2 style='color: green;'>This is WHITE LABEL PARTNER 1</h2>";
    echo "<p>Logic: Load Partner 1's logo, colors, and content.</p>";
} elseif ($host === 'finance-pro.test') {
    echo "<h2 style='color: purple;'>This is WHITE LABEL PARTNER 2</h2>";
    echo "<p>Logic: Load Partner 2's logo, colors, and content.</p>";
} else {
    echo "<h2 style='color: red;'>Unknown Domain</h2>";
    echo "<p>This domain is pointing here but is not recognized in our test logic.</p>";
}

echo "<hr>";
echo "<h3>Server Details:</h3>";
echo "<pre>";
print_r($_SERVER);
echo "</pre>";
?>
