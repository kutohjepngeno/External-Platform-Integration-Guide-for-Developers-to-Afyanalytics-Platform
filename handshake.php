<?php
require_once __DIR__ . '/config.php';

function initiate_handshake() {
    $url = BASE_URL . '/initiate-handshake';
    $payload = [
        'platform_name' => PLATFORM_NAME,
        'platform_key' => PLATFORM_KEY,
        'platform_secret' => PLATFORM_SECRET,
        'callback_url' => CALLBACK_URL
    ];
    
    log_message('INFO', "Initiating handshake to $url");
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        log_message('ERROR', "cURL error: $error");
        return [null, null];
    }
    
    $data = json_decode($response, true);
    if ($http_code === 200 && isset($data['success']) && $data['success'] === true) {
        $handshake_token = $data['data']['handshake_token'];
        $expires_at = $data['data']['expires_at'];
        log_message('INFO', "Handshake initiated. Token: $handshake_token, expires: $expires_at");
        return [$handshake_token, $expires_at];
    } else {
        log_message('ERROR', "Initiate failed: " . print_r($data, true));
        return [null, null];
    }
}

function complete_handshake($handshake_token) {
    $url = BASE_URL . '/complete-handshake';
    $payload = [
        'handshake_token' => $handshake_token,
        'platform_key' => PLATFORM_KEY
    ];
    
    log_message('INFO', "Completing handshake with token: $handshake_token");
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        log_message('ERROR', "cURL error: $error");
        return [false, ['error' => $error]];
    }
    
    $data = json_decode($response, true);
    if ($http_code === 200 && isset($data['success']) && $data['success'] === true) {
        $access_token = $data['data']['access_token'];
        $refresh_token = $data['data']['refresh_token'];
        $expires_at = $data['data']['expires_at'];
        log_message('INFO', "Handshake completed. Access token: " . substr($access_token, 0, 10) . "...");
        // store tokens
        set_token_store('access_token', $access_token);
        set_token_store('refresh_token', $refresh_token);
        set_token_store('expires_at', $expires_at);
        return [true, $data['data']];
    } else {
        $error_msg = $data['message'] ?? 'Unknown error';
        log_message('ERROR', "Complete handshake failed: $error_msg");
        if ($http_code === 401) log_message('WARNING', "Handshake token expired or invalid.");
        return [false, $data];
    }
}

function perform_full_handshake() {
    // Step 1
    list($handshake_token, $expires_at) = initiate_handshake();
    if (!$handshake_token) {
        return ['success' => false, 'message' => 'Failed to initiate handshake'];
    }
    
    // Step 2 – complete immediately
    list($success, $result) = complete_handshake($handshake_token);
    if ($success) {
        return ['success' => true, 'message' => 'Handshake successful', 'data' => $result];
    } else {
        // Retry once if expired
        if (isset($result['error']) && strpos(strtolower($result['error']), 'expired') !== false ||
            (isset($result['message']) && strpos(strtolower($result['message']), 'expired') !== false)) {
            log_message('INFO', 'Retrying handshake due to possible expiry.');
            list($handshake_token2, $_) = initiate_handshake();
            if ($handshake_token2) {
                list($success2, $result2) = complete_handshake($handshake_token2);
                if ($success2) {
                    return ['success' => true, 'message' => 'Handshake successful after retry', 'data' => $result2];
                }
            }
        }
        return ['success' => false, 'message' => 'Handshake failed', 'details' => $result];
    }
}

// If called directly (CLI or web), execute the handshake and output JSON
if (php_sapi_name() === 'cli' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $result = perform_full_handshake();
    echo json_encode($result, JSON_PRETTY_PRINT);
    exit;
}
?>
