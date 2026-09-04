<?php
header('Content-Type: application/json');

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
    
    // Check device limit
    if ($key_data['devices_used'] >= $key_data['max_devices']) {
        echo json_encode(["ok" => false, "error" => "Device limit reached"], JSON_PRETTY_PRINT);
        exit;
    }
    
    // Assign device if not already used
    if ($key_data['device_id'] === null) {
        $key_data['device_id'] = $device_id;
        $key_data['devices_used'] = 1;
        $keys[$key_name] = $key_data;
        file_put_contents($store_file, json_encode($keys, JSON_PRETTY_PRINT));
    } elseif ($key_data['device_id'] !== $device_id) {
        echo json_encode(["ok" => false, "error" => "Device mismatch"], JSON_PRETTY_PRINT);
        exit;
    }
    
    // Return success
    $response = [
        "ok" => true,
        "status" => true,
        "Status" => "active",
        "DeviceLimit" => $key_data['max_devices'],
        "Devices" => $key_data['device_id'] ?: $device_id,
        "device_id" => $key_data['device_id'] ?: $device_id,
        "devices_used" => $key_data['devices_used'],
        "max_devices" => $key_data['max_devices'],
        "Expiry" => $key_data['expiry_timestamp'],
        "Vaildity" => $key_data['expiry_timestamp'],
        "Validity" => $key_data['expiry_timestamp'],
        "state" => "active",
        "cheat" => null,
        "seller" => "",
        "validity" => $key_data['validity'],
        "expires_at" => $key_data['expires_at'],
        "remaining_seconds" => max(0, ($key_data['expiry_timestamp'] - time() * 1000) / 1000),
        "remaining" => "5h 0m",
        "hmac" => hash('sha256', $key_name . $nonce . 'jitu-secret'),
        "nonce" => $nonce
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} else {
    echo json_encode(["ok" => false, "error" => "Invalid key"], JSON_PRETTY_PRINT);
}
?>
