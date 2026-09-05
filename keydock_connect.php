<?php
header('Content-Type: application/json');

// Set timezone to IST (India)
date_default_timezone_set('Asia/Kolkata');

$key_name = $_GET['key_name'] ?? $_POST['key_name'] ?? '';
$device_id = $_GET['device_id'] ?? $_POST['device_id'] ?? 'android-test';
$nonce = $_GET['nonce'] ?? $_POST['nonce'] ?? 'jitu-app';

$store_file = 'keys.json';

// Load keys
$keys = [];
if (file_exists($store_file)) {
    $keys = json_decode(file_get_contents($store_file), true) ?? [];
}

// Check if key exists
if (isset($keys[$key_name])) {
    $key_data = $keys[$key_name];
    
    // Check expiry
    if ($key_data['expiry_timestamp'] < time() * 1000) {
        echo json_encode(["ok" => false, "error" => "Key expired"], JSON_PRETTY_PRINT);
        exit;
    }
    
    // --- DEVICE CHECK LOGIC ---
    // If key has no device assigned yet → assign this device
    if ($key_data['device_id'] === null) {
        $key_data['device_id'] = $device_id;
        $key_data['devices_used'] = 1;
        $keys[$key_name] = $key_data;
        file_put_contents($store_file, json_encode($keys, JSON_PRETTY_PRINT));
    } 
    // If key has same device → ALLOW (no block)
    elseif ($key_data['device_id'] === $device_id) {
        // Same device, allow access
        // Do nothing, just proceed
    } 
    // If key has different device → CHECK DEVICE LIMIT
    else {
        // Check if device limit allows more devices
        if ($key_data['devices_used'] < $key_data['max_devices']) {
            // Add new device
            $key_data['device_id'] = $device_id; // Store last used device
            $key_data['devices_used'] = $key_data['devices_used'] + 1;
            $keys[$key_name] = $key_data;
            file_put_contents($store_file, json_encode($keys, JSON_PRETTY_PRINT));
        } else {
            echo json_encode(["ok" => false, "error" => "Device limit reached! Max devices: " . $key_data['max_devices']], JSON_PRETTY_PRINT);
            exit;
        }
    }
    
    // Calculate remaining time
    $now = time();
    $expiry = $key_data['expiry_timestamp'] / 1000;
    $remaining = max(0, $expiry - $now);
    $remaining_hours = floor($remaining / 3600);
    $remaining_minutes = floor(($remaining % 3600) / 60);
    
    // Return success
    $response = [
        "ok" => true,
        "status" => true,
        "Status" => "active",
        "DeviceLimit" => $key_data['max_devices'],
        "Devices" => $key_data['device_id'],
        "device_id" => $key_data['device_id'],
        "devices_used" => $key_data['devices_used'],
        "max_devices" => $key_data['max_devices'],
        "Expiry" => $key_data['expiry_timestamp'],
        "Vaildity" => $key_data['expiry_timestamp'],
        "Validity" => $key_data['expiry_timestamp'],
        "state" => "active",
        "cheat" => null,
        "seller" => "",
        "validity" => $key_data['validity'],
        "expires_at" => date('Y-m-d H:i:s', $expiry),
        "remaining_seconds" => $remaining,
        "remaining" => $remaining_hours . "h " . $remaining_minutes . "m",
        "hmac" => hash('sha256', $key_name . $nonce . 'jitu-secret'),
        "nonce" => $nonce
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} else {
    echo json_encode(["ok" => false, "error" => "Invalid key"], JSON_PRETTY_PRINT);
}
?>
