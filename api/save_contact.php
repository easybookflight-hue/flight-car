<?php
/* ==========================================================
   api/save_contact.php — Sheet3 Logger (Contact Form)
   Flow: Contact Form → This API → Google Sheet + CSV backup
   ========================================================== */
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$body = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$name    = trim($body['name'] ?? $body['customer_name'] ?? '');
$email   = trim($body['email'] ?? '');
$phone   = trim($body['phone'] ?? '');
if (strpos($phone, '+') === 0) { $phone = "'" . $phone; }
$message = trim($body['message'] ?? '');

$data = [
    'action'     => 'contact',
    'timestamp'  => date('Y-m-d H:i:s'),
    'name'       => $name,
    'email'      => $email,
    'phone'      => $phone,
    'message'    => $message,
    'user_ip'    => $_SERVER['REMOTE_ADDR'] ?? ''
];

$results = [];

// 1. Send to Google Sheet Webhook
$sheetSent = false;
if (defined('GOOGLE_SHEET_WEBHOOK_URL') && GOOGLE_SHEET_WEBHOOK_URL) {
    try {
        $sheetPayload = json_encode($data);
        $sheetOpts = ['http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/json\r\nContent-Length: " . strlen($sheetPayload) . "\r\n",
            'content'       => $sheetPayload,
            'timeout'       => 10,
            'ignore_errors' => true,
        ]];
        @file_get_contents(GOOGLE_SHEET_WEBHOOK_URL, false, stream_context_create($sheetOpts));
        $sheetSent = true;
    } catch (Exception $e) {
        $results['sheet_error'] = $e->getMessage();
    }
}
$results['sheet'] = $sheetSent ? 'sent' : 'skipped';

// 2. Save locally to CSV (backup)
$dir = __DIR__ . '/../data';
if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
$file = $dir . '/sheet3_contacts.csv';
$isNew = !file_exists($file);
$fp = @fopen($file, 'a');
if ($fp) {
    if ($isNew) {
        fputcsv($fp, ['Timestamp', 'Full Name', 'Email', 'Phone', 'Message', 'IP Address']);
    }
    fputcsv($fp, [
        $data['timestamp'], $data['name'], $data['email'], $data['phone'],
        $data['message'], $data['user_ip']
    ]);
    fclose($fp);
}
$results['csv'] = 'saved';

echo json_encode(['status' => 'success', 'sheet' => 'Sheet3', 'results' => $results]);
