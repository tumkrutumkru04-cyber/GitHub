<?php
header('Content-Type: application/json');

// Set timezone to IST (India)
date_default_timezone_set('Asia/Kolkata');

$store_file = 'keys.json';

// Get parameters
$custom_key = $_GET['key'] ?? $_POST['key'] ?? '';
$days = intval($_GET['days'] ?? $_POST['days'] ?? 0);
$devices = intval($_GET['devices'] ?? $_POST['devices'] ?? 0);

$keys = [];
if (file_exists($store_file)) {
    $keys = json_decode(file_get_contents($store_file), true) ?? [];
}

if (empty($custom_key)) {
    echo json_encode(["ok" => false, "error" => "Key parameter required (e.g., ?key=MYKEY&days=5&devices=3)"], JSON_PRETTY_PRINT);
    exit;
}

if ($days <= 0) {
    echo json_encode(["ok" => false, "error" => "Days must be > 0"], JSON_PRETTY_PRINT);
    exit;
}

if ($devices <= 0) {
    echo json_encode(["ok" => false, "error" => "Devices must be > 0"], JSON_PRETTY_PRINT);
    exit;
}

// Check if key already exists
if (isset($keys[$custom_key])) {
    echo json_encode(["ok" => false, "error" => "Key already exists"], JSON_PRETTY_PRINT);
    exit;
}

// Calculate expiry
$expiry = time() + ($days * 24 * 3600);
$validity_text = $days . " Days";

// Store key
$keys[$custom_key] = [
    "key" => $custom_key,
    "device_id" => null,
    "devices_used" => 0,
    "max_devices" => $devices,
    "created_at" => date('Y-m-d H:i:s'),
    "expires_at" => date('Y-m-d H:i:s', $expiry),
    "expiry_timestamp" => $expiry * 1000,
    "validity" => $validity_text,
    "status" => "active"
];

file_put_contents($store_file, json_encode($keys, JSON_PRETTY_PRINT));

echo json_encode([
    "ok" => true,
    "key" => $custom_key,
    "validity" => $validity_text,
    "expires_at" => date('Y-m-d H:i:s', $expiry),
    "max_devices" => $devices,
    "devices_used" => 0
], JSON_PRETTY_PRINT);
?>
