<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

$store = get_token_store();
if (isset($store['access_token'])) {
    echo json_encode([
        'has_access_token' => true,
        'expires_at' => $store['expires_at'],
        'platform' => PLATFORM_NAME
    ]);
} else {
    echo json_encode(['has_access_token' => false]);
}
?>
