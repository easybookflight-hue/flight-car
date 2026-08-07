<?php
/* ==========================================================
   api/booking.php — Ignav booking-links proxy
   POST JSON body: {ignav_id: "..."}
   ========================================================== */
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?: [];
} else {
    $body = ['ignav_id' => $_GET['ignav_id'] ?? ''];
}

if (empty($body['ignav_id'])) {
    http_response_code(400);
    echo '{"error":"Missing ignav_id"}';
    exit;
}

$url     = IGNAV_BASE . '/api/fares/booking-links';
$payload = json_encode(['ignav_id' => $body['ignav_id']]);

$opts = ['http' => [
    'method'  => 'POST',
    'header'  => "X-Api-Key: " . IGNAV_API_KEY . "\r\n" .
                 "Content-Type: application/json\r\n" .
                 "Content-Length: " . strlen($payload) . "\r\n" .
                 "User-Agent: SkyFare/1.0\r\n",
    'content' => $payload,
    'timeout' => 40,
    'ignore_errors' => true,
]];
$result = @file_get_contents($url, false, stream_context_create($opts));
$code   = 0;
foreach ($http_response_header ?? [] as $h) {
    if (preg_match('#HTTP/\S+\s+(\d+)#', $h, $m)) $code = (int)$m[1];
}
http_response_code($code ?: 500);
echo $result !== false ? $result : '{"error":"Upstream error"}';
