<?php

// Always start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Force JSON response
header('Content-Type: application/json');

// Prevent PHP warnings from corrupting JSON
error_reporting(0);
ini_set('display_errors', 0);

// Default response
$response = [
    'authenticated' => false,
    'user' => null
];

try {

    if (isset($_SESSION['user_id'])) {
        $response['authenticated'] = true;

        $response['user'] = [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? null,
            'role' => $_SESSION['role'] ?? null
        ];
    }

} catch (Exception $e) {
    $response['authenticated'] = false;
}

// Output ONLY JSON
echo json_encode($response);
exit;