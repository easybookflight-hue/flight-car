<?php
/* ===================================================================
   api/save-cab-booking.php
   Endpoint to log Car Rental Leads locally to CSV & POST to Google Sheets
   =================================================================== */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../includes/config.php';

// 1. Parse JSON or Form POST data
$inputData = json_decode(file_get_contents('php://input'), true);
if (!$inputData) {
    $inputData = $_POST;
}

$rideType       = isset($inputData['ride_type']) ? trim($inputData['ride_type']) : 'Airport Transfer';
$direction      = isset($inputData['direction']) ? trim($inputData['direction']) : 'To Destination';
$vehicleType    = isset($inputData['vehicle_type']) ? trim($inputData['vehicle_type']) : 'Sedan';
$pickupAddress  = isset($inputData['pickup_address']) ? trim($inputData['pickup_address']) : '';
$pickupDate     = isset($inputData['pickup_date']) ? trim($inputData['pickup_date']) : '';
$pickupTime     = isset($inputData['pickup_time']) ? trim($inputData['pickup_time']) : '';
$estimatedFare  = isset($inputData['estimated_fare']) ? trim($inputData['estimated_fare']) : '';
$customerName   = isset($inputData['name']) ? trim($inputData['name']) : '';
$customerPhone  = isset($inputData['phone']) ? trim($inputData['phone']) : '';
$customerEmail  = isset($inputData['email']) ? trim($inputData['email']) : '';
$flightNumber   = isset($inputData['flight_number']) ? trim($inputData['flight_number']) : '';
$specialNotes   = isset($inputData['special_notes']) ? trim($inputData['special_notes']) : '';
$googleSheetUrl = !empty($inputData['google_sheet_url']) ? trim($inputData['google_sheet_url']) : (defined('GOOGLE_SHEET_WEBHOOK_URL') ? GOOGLE_SHEET_WEBHOOK_URL : '');

$timestamp      = date('Y-m-d H:i:s');
$clientIp       = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$userAgent      = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

// Validate required fields
if (empty($customerName) || empty($customerPhone) || empty($pickupAddress)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Name, Phone, and Pickup Address are required.'
    ]);
    exit;
}

// 2. Save lead locally to data/cab_bookings.csv
$dataDir = __DIR__ . '/../data';
if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0755, true);
}

$csvFile = $dataDir . '/cab_bookings.csv';
$isNewFile = !file_exists($csvFile);

$fileHandle = fopen($csvFile, 'a');
if ($fileHandle) {
    if ($isNewFile) {
        fputcsv($fileHandle, [
            'Timestamp', 'Ride Type', 'Direction', 'Vehicle', 'Pickup Address',
            'Pickup Date', 'Pickup Time', 'Estimated Fare', 'Customer Name',
            'Phone', 'Email', 'Flight No', 'Special Notes', 'IP Address'
        ]);
    }

    fputcsv($fileHandle, [
        $timestamp, $rideType, $direction, $vehicleType, $pickupAddress,
        $pickupDate, $pickupTime, $estimatedFare, $customerName,
        $customerPhone, $customerEmail, $flightNumber, $specialNotes, $clientIp
    ]);
    fclose($fileHandle);
}

// 3. Optional Forwarding to Google Apps Script Web App (if configured)
$postData = [
    'action'         => 'cab_booking',
    'timestamp'      => $timestamp,
    'ride_type'      => $rideType,
    'direction'      => $direction,
    'vehicle_type'   => $vehicleType,
    'pickup_address' => $pickupAddress,
    'pickup_date'    => $pickupDate,
    'pickup_time'    => $pickupTime,
    'estimated_fare' => $estimatedFare,
    'customer_name'  => $customerName,
    'customer_phone' => $customerPhone,
    'customer_email' => $customerEmail,
    'flight_number'  => $flightNumber,
    'special_notes'  => $specialNotes,
    'client_ip'      => $clientIp
];

$gSheetSent = false;
if (!empty($googleSheetUrl) && filter_var($googleSheetUrl, FILTER_VALIDATE_URL)) {
    $ch = curl_init($googleSheetUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    curl_close($ch);
    $gSheetSent = true;
}

echo json_encode([
    'status'  => 'success',
    'message' => 'Lead captured successfully and saved to database/CSV.',
    'lead'    => $postData,
    'gsheet_sent' => $gSheetSent
]);
