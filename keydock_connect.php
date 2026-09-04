<?php
header('Content-Type: application/json');

// Get request parameters
$key_name = isset($_GET['key_name']) ? $_GET['key_name'] : (isset($_POST['key_name']) ? $_POST['key_name'] : '');
$device_id = isset($_GET['device_id']) ? $_GET['device_id'] : (isset($_POST['device_id']) ? $_POST['device_id'] : '');
$nonce = isset($_GET['nonce']) ? $_GET['nonce'] : (isset($_POST['nonce']) ? $_POST['nonce'] : '');

// If key_name is 'piyushacks' — return success
if ($key_name === 'piyushacks') {
    
    // 5 hours from now
    $expiry_time = time() + (5 * 3600); // 5 hours
    $expiry_date = date('Y-m-d H:i:s', $expiry_time);
    
    $response = [
        "ok" => true,
        "status" => true,
        "Status" => "active",
        "DeviceLimit" => 1,                    // ONLY 1 DEVICE
        "Devices" => $device_id ?: "android-test",
        "device_id" => $device_id ?: "android-test",
        "devices_used" => 1,
        "max_devices" => 1,                    // MAX 1 DEVICE
        "Expiry" => $expiry_time * 1000,       // Milliseconds
        "Vaildity" => $expiry_time * 1000,
        "Validity" => $expiry_time * 1000,
        "state" => "active",
        "cheat" => null,
        "seller" => "",
        "validity" => "5 Hours",
        "expires_at" => $expiry_date,
        "remaining_seconds" => 5 * 3600,
        "remaining" => "5h 0m",
        "hmac" => hash('sha256', $key_name . $nonce . 'jitu-secret'),
        "nonce" => $nonce ?: "jitu-app"
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
} else {
    // Invalid key
    echo json_encode([
        "ok" => false,
        "error" => "Key name required or invalid"
    ], JSON_PRETTY_PRINT);
}
?>
