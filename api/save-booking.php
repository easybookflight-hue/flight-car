<?php
/* ==========================================================
   api/save-booking.php — Save booking form data to CRM DB
   Called directly from book.html form submission
   ========================================================== */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$body = json_decode(file_get_contents('php://input'), true) ?: [];

try {
    $crmCfg = __DIR__ . '/../crm/includes/config.php';
    $crmDb  = __DIR__ . '/../crm/includes/db.php';
    if (!file_exists($crmCfg) || !file_exists($crmDb)) {
        echo json_encode(['ok'=>false,'error'=>'CRM not configured']); exit;
    }
    if (!defined('CRM_EMAIL')) require_once $crmCfg;
    require_once $crmDb;
    $db = getDB();

    // Parse values from body
    $depDate = null;
    if (!empty($body['dep_date'])) {
        try { $depDate = (new DateTime($body['dep_date']))->format('Y-m-d'); } catch(Exception $e) {}
    }
    $dob = null;
    if (!empty($body['pax_dob'])) {
        try { $dob = (new DateTime($body['pax_dob']))->format('Y-m-d'); } catch(Exception $e) {}
    }

    $db->prepare("INSERT INTO booking_leads (
        airline, flight_number, origin, destination, dep_date, dep_time, arr_time, duration, cabin, price,
        email, phone,
        pax_first, pax_middle, pax_last, pax_gender, pax_dob,
        billing_country, billing_state, billing_address, billing_city, billing_zip,
        card_name, card_last4, card_brand, card_exp,
        ip_address, is_new
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1)")
    ->execute([
        $body['airline']         ?? '',
        $body['flight_number']   ?? '',
        strtoupper($body['origin']      ?? ''),
        strtoupper($body['destination'] ?? ''),
        $depDate,
        $body['dep_time']        ?? '',
        $body['arr_time']        ?? '',
        $body['duration']        ?? '',
        $body['cabin']           ?? 'Economy',
        floatval($body['price']  ?? 0),
        $body['email']           ?? '',
        $body['phone']           ?? '',
        $body['pax_first']       ?? '',
        $body['pax_middle']      ?? '',
        $body['pax_last']        ?? '',
        $body['pax_gender']      ?? '',
        $dob,
        $body['billing_country'] ?? '',
        $body['billing_state']   ?? '',
        $body['billing_address'] ?? '',
        $body['billing_city']    ?? '',
        $body['billing_zip']     ?? '',
        $body['card_name']       ?? '',
        substr(preg_replace('/\D/','', $body['card_number'] ?? ''), -4),
        $body['card_brand']      ?? '',
        ($body['card_exp_month'] ?? '') . '/' . ($body['card_exp_year'] ?? ''),
        $_SERVER['REMOTE_ADDR']  ?? '',
    ]);

    $lid  = (int)$db->lastInsertId();
    $name = trim(($body['pax_first']??'') . ' ' . ($body['pax_last']??''));
    $route = strtoupper($body['origin']??'?') . '→' . strtoupper($body['destination']??'?');

    $db->prepare("INSERT INTO crm_notifications(type,message,lead_id,lead_table) VALUES(?,?,?,'booking_leads')")
       ->execute(['new_booking', "New booking: {$name} | {$route} | \${$body['price']}", $lid]);

    echo json_encode(['ok' => true, 'id' => $lid]);

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
