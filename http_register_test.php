<?php
// Usage: php scripts/http_register_test.php
$base = 'http://localhost/final-blog-projec';
$registerUrl = $base . '/auth/register.php';

$cookieFile = sys_get_temp_dir() . '/reg_test_cookies.txt';
@unlink($cookieFile);

// Get the register page
$ch = curl_init($registerUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
$html = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);
if ($html === false) {
    echo "GET failed: $err\n";
    exit(1);
}

// extract csrf
if (preg_match('/name="csrf" value="([^"]+)"/', $html, $m)) {
    $token = $m[1];
    echo "Found CSRF token: $token\n";
} else {
    echo "CSRF token not found in GET response\n";
    exit(1);
}

$user = 'testuser' . rand(1000,9999);
$pw = 'pass1234';
$postFields = http_build_query([
    'csrf' => $token,
    'username' => $user,
    'password' => $pw,
]);

$ch = curl_init($registerUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo "POST failed: $err\n";
    exit(1);
}

echo "POST HTTP code: $http\n";
// Simple check: after success we redirect to login page; check if '/auth/login.php' appears
if (strpos($response, 'Login') !== false) {
    echo "Registration appears successful, login page content detected.\n";
} else {
    echo "Registration response length: " . strlen($response) . "\n";
}

unlink($cookieFile);

?>