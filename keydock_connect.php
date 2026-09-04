<?php
header('Content-Type: application/json');

$key_name = $_GET['key_name'] ?? $_POST['key_name'] ?? '';
$device_id = $_GET['device_id'] ?? $_POST['device_id'] ?? 'android-test';
$nonce = $_GET['nonce'] ?? $_POST['nonce'] ?? 'jitu-app';

if ($key_name === 'piyushacks') {
    $expiry_time = time() + (5 * 3600);
    $response = [
        "ok" => true,
        "status" => true,
        "Status" => "active",
        "DeviceLimit" => 1,
        "Devices" => $device_id,
        "device_id" => $device_id,
        "devices_used" => 1,
        "max_devices" => 1,
        "Expiry" => $expiry_time * 1000,
        "Vaildity" => $expiry_time * 1000,
        "Validity" => $expiry_time * 1000,
        "state" => "active",
        "cheat" => null,
        "seller" => "",
        "validity" => "5 Hours",
        "expires_at" => date('Y-m-d H:i:s', $expiry_time),
        "remaining_seconds" => 18000,
        "remaining" => "5h 0m",
        "hmac" => hash('sha256', $key_name . $nonce . 'jitu-secret'),
        "nonce" => $nonce
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
} else {
    echo json_encode(["ok" => false, "error" => "Key name required or invalid"]);
}
?>
