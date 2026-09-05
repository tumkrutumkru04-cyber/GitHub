<?php
header('Content-Type: application/json');

// Set timezone to IST (India)
date_default_timezone_set('Asia/Kolkata');

$store_file = 'keys.json';

// Get parameters
$format = $_GET['format'] ?? $_POST['format'] ?? 'PIYUSH';
$days = intval($_GET['days'] ?? $_POST['days'] ?? 1);
$max_devices = intval($_GET['max_devices'] ?? $_POST['max_devices'] ?? 1);

// Validate
if ($days < 1 || $days > 365) $days = 1;
if ($max_devices < 1 || $max_devices > 100) $max_devices = 1;

function generateKey($format) {
    $random = strtoupper(bin2hex(random_bytes(4)));
    
    switch ($format) {
        case 'PIYUSH':
            return "PIYUSH-HACKS-" . $random;
        case 'HEX':
            return "HEX-CHATS-MOCO-" . $random;
        case 'XITEXE':
            $p1 = strtoupper(bin2hex(random_bytes(2)));
            $p2 = strtoupper(bin2hex(random_bytes(2)));
            $p3 = strtoupper(bin2hex(random_bytes(2)));
            return "XITEXE-" . $p1 . "-" . $p2 . "-" . $p3;
        case 'CUSTOM':
            $prefix = $_GET['prefix'] ?? $_POST['prefix'] ?? 'CUSTOM';
            return strtoupper($prefix) . "-" . $random;
        default:
            return "KEY-" . $random;
    }
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'generate';

$keys = [];
if (file_exists($store_file)) {
    $keys = json_decode(file_get_contents($store_file), true) ?? [];
}

if ($action === 'generate') {
    $new_key = generateKey($format);
    $expiry = time() + ($days * 24 * 3600); // Days in seconds
    
    $keys[$new_key] = [
        "key" => $new_key,
        "device_id" => null,
        "devices_used" => 0,
        "max_devices" => $max_devices,
        "created_at" => date('Y-m-d H:i:s'),
        "expires_at" => date('Y-m-d H:i:s', $expiry),
        "expiry_timestamp" => $expiry * 1000,
        "validity" => $days . " Days",
        "status" => "active",
        "format" => $format
    ];
    
    file_put_contents($store_file, json_encode($keys, JSON_PRETTY_PRINT));
    
    echo json_encode([
        "ok" => true,
        "key" => $new_key,
        "validity" => $days . " Days",
        "expires_at" => date('Y-m-d H:i:s', $expiry),
        "max_devices" => $max_devices,
        "format" => $format
    ], JSON_PRETTY_PRINT);
    
} elseif ($action === 'list') {
    $list = [];
    foreach ($keys as $k => $data) {
        $list[] = [
            "key" => $k,
            "status" => $data['status'] ?? 'active',
            "devices_used" => $data['devices_used'] ?? 0,
            "max_devices" => $data['max_devices'] ?? 1,
            "expires_at" => $data['expires_at'] ?? 'N/A'
        ];
    }
    echo json_encode(["ok" => true, "keys" => $list], JSON_PRETTY_PRINT);
    
} else {
    echo json_encode(["ok" => false, "error" => "Invalid action"]);
}
?>
