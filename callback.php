<?php
require_once __DIR__ . '/logger.php';
$input = file_get_contents('php://input');
log_message('INFO', "Received callback: $input");
header('Content-Type: application/json');
echo json_encode(['status' => 'received']);
?>
