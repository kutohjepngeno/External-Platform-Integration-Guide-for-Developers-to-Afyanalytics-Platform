<?php
function log_message($level, $message) {
    $log_dir = __DIR__ . '/logs';
    if (!is_dir($log_dir)) mkdir($log_dir, 0777, true);
    $log_file = $log_dir . '/handshake.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    // Also output to console if running in CLI
    if (php_sapi_name() === 'cli') echo $log_entry;
}
?>
