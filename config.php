<?php
require_once __DIR__ . '/logger.php';

$env_file = __DIR__ . '/.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

define('PLATFORM_NAME', getenv('PLATFORM_NAME'));
define('PLATFORM_KEY', getenv('PLATFORM_KEY'));
define('PLATFORM_SECRET', getenv('PLATFORM_SECRET'));
define('BASE_URL', getenv('BASE_URL'));
define('CALLBACK_URL', getenv('CALLBACK_URL'));

// In-memory token store (for demo – use database/Redis in production)
$token_store = [];
session_start(); // use session as simple storage for demo
if (!isset($_SESSION['token_store'])) $_SESSION['token_store'] = [];

function get_token_store() {
    return $_SESSION['token_store'];
}

function set_token_store($key, $value) {
    $_SESSION['token_store'][$key] = $value;
}
?>
