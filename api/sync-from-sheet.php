<?php
/* ==========================================================
   api/sync-from-sheet.php — Google Apps Script → CRM Bridge
   
   Called BY Google Apps Script when new data is appended to Sheet.
   This creates the "Sheet → CRM" direction of the flow.
   
   How to configure Google Apps Script:
   In your doPost(e) function, add at the end:
   
     var crmUrl = 'https://YOURSITE.com/api/sync-from-sheet.php';
     UrlFetchApp.fetch(crmUrl, {
       method: 'post',
       contentType: 'application/json',
       payload: JSON.stringify(rowData),
       muteHttpExceptions: true
     });
   ========================================================== */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// ── Security: optional secret token check ──────────────────
// Uncomment and set a token in your Google Apps Script call too:
// $expectedToken = 'CHANGE_ME_SECRET_TOKEN_123';
// $token = $_SERVER['HTTP_X_SYNC_TOKEN'] ?? '';
// if ($token !== $expectedToken) {
//     http_response_code(401);
//     echo json_encode(['error' => 'Unauthorized']);
//     exit;
// }

$body = json_decode(file_get_contents('php://input'), true) ?: [];

if (empty($body)) {
    echo json_encode(['ok' => false, 'error' => 'Empty payload']);
    exit;
}

try {
    $crmCfg = __DIR__ . '/../crm/includes/config.php';
    $crmDb  = __DIR__ . '/../crm/includes/db.php';
    if (!file_exists($crmCfg) || !file_exists($crmDb)) {
        echo json_encode(['ok' => false, 'error' => 'CRM not configured']);
        exit;
    }
    if (!defined('CRM_EMAIL')) require_once $crmCfg;
    require_once $crmDb;
    $db = getDB();

    $action = strtolower(trim($body['action'] ?? ''));

    // ── Sheet1 (Search Lead) ──────────────────────────────────
    if ($action === 'sheet1' || $action === 'search') {
        $origin      = strtoupper(trim($body['origin'] ?? $body['from'] ?? ''));
        $destination = strtoupper(trim($body['destination'] ?? $body['to'] ?? ''));
        $dep_date    = trim($body['departure_date'] ?? $body['dep_date'] ?? '');
        $return_date = trim($body['return_date'] ?? '');
        $trip_type   = trim($body['trip_type'] ?? 'oneway');
        $adults      = (int)($body['adults'] ?? $body['passengers'] ?? 1);
        $cabin       = trim($body['cabin_class'] ?? $body['cabin'] ?? 'economy');
        $name        = trim($body['customer_name'] ?? $body['name'] ?? '');
        $phone       = trim($body['customer_phone'] ?? $body['phone'] ?? '');

        $depDateDb   = null;
        $retDateDb   = null;
        if (!empty($dep_date)) {
            try { $depDateDb = (new DateTime($dep_date))->format('Y-m-d'); } catch (Exception $e) {}
        }
        if (!empty($return_date)) {
            try { $retDateDb = (new DateTime($return_date))->format('Y-m-d'); } catch (Exception $e) {}
        }
        $isRoundTrip = in_array(strtolower($trip_type), ['round-trip', 'roundtrip', 'return']);

        // ── Deduplication Check: Prevent Duplicate Insertions ─────
        $dupCheck = $db->prepare("SELECT id FROM search_leads WHERE (phone = ? OR customer_name = ?) AND origin = ? AND destination = ? AND created_at >= NOW() - INTERVAL 5 MINUTE LIMIT 1");
        $dupCheck->execute([$phone, $name, $origin, $destination]);
        $existing = $dupCheck->fetch();
        if ($existing) {
            echo json_encode(['ok' => true, 'action' => 'search_lead', 'id' => (int)$existing['id'], 'duplicate' => true, 'message' => 'Lead already exists']);
            exit;
        }

        $db->prepare("INSERT INTO search_leads
            (origin, destination, origin_name, dest_name, dep_date, return_date, trip_type,
             passengers, cabin, customer_name, phone, ip_address, is_new)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,1)")
           ->execute([
               $origin, $destination, $origin, $destination,
               $depDateDb, $isRoundTrip ? $retDateDb : null,
               $isRoundTrip ? 'roundtrip' : 'oneway',
               $adults, $cabin, $name, $phone,
               $_SERVER['REMOTE_ADDR'] ?? 'sheets-sync'
           ]);

        $lid = (int)$db->lastInsertId();
        $db->prepare("INSERT INTO crm_notifications (type, message, lead_id, lead_table) VALUES (?,?,?,'search_leads')")
           ->execute(['new_search', "Sheet sync: {$origin}→{$destination} | {$name}", $lid]);

        echo json_encode(['ok' => true, 'action' => 'search_lead', 'id' => $lid]);

    // ── Sheet2 (Booking Lead) ─────────────────────────────────
    } elseif ($action === 'sheet2' || $action === 'booking') {
        $origin      = strtoupper(trim($body['origin'] ?? ''));
        $destination = strtoupper(trim($body['destination'] ?? ''));
        $email       = trim($body['email'] ?? '');
        $phone       = trim($body['phone'] ?? '');
        $paxFirst    = trim($body['pax_first'] ?? $body['pax_first_name'] ?? '');
        $paxMiddle   = trim($body['pax_middle'] ?? $body['pax_middle_name'] ?? '');
        $paxLast     = trim($body['pax_last'] ?? $body['pax_last_name'] ?? '');
        $paxGender   = trim($body['pax_gender'] ?? '');
        $price       = floatval($body['price'] ?? $body['total_price'] ?? 0);
        $airline     = trim($body['airline'] ?? '');
        $cardLast4   = substr(preg_replace('/\D/', '', $body['card_number'] ?? ''), -4);
        $cardBrand   = trim($body['card_brand'] ?? '');
        $cardName    = trim($body['card_name'] ?? '');
        $depDateDb   = null;
        $dobDb       = null;
        if (!empty($body['dep_date'])) {
            try { $depDateDb = (new DateTime($body['dep_date']))->format('Y-m-d'); } catch (Exception $e) {}
        }
        if (!empty($body['pax_dob'])) {
            try { $dobDb = (new DateTime($body['pax_dob']))->format('Y-m-d'); } catch (Exception $e) {}
        }

        // ── Deduplication Check: Prevent Duplicate Insertions ─────
        $dupCheck = $db->prepare("SELECT id FROM booking_leads WHERE (phone = ? OR email = ? OR (pax_first = ? AND pax_last = ?)) AND origin = ? AND destination = ? AND created_at >= NOW() - INTERVAL 5 MINUTE LIMIT 1");
        $dupCheck->execute([$phone, $email, $paxFirst, $paxLast, $origin, $destination]);
        $existing = $dupCheck->fetch();
        if ($existing) {
            echo json_encode(['ok' => true, 'action' => 'booking_lead', 'id' => (int)$existing['id'], 'duplicate' => true, 'message' => 'Booking lead already exists']);
            exit;
        }

        $db->prepare("INSERT INTO booking_leads (
            airline, flight_number, origin, destination, dep_date, cabin, price,
            email, phone, pax_first, pax_middle, pax_last, pax_gender, pax_dob,
            billing_country, billing_state, billing_address, billing_city, billing_zip,
            card_name, card_last4, card_brand, ip_address, is_new
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1)")
        ->execute([
            $airline,
            trim($body['flight_number'] ?? ''),
            $origin, $destination, $depDateDb,
            trim($body['cabin'] ?? $body['cabin_class'] ?? 'Economy'),
            $price, $email, $phone,
            $paxFirst, $paxMiddle, $paxLast, $paxGender, $dobDb,
            trim($body['billing_country'] ?? ''),
            trim($body['billing_state'] ?? ''),
            trim($body['billing_address'] ?? ''),
            trim($body['billing_city'] ?? ''),
            trim($body['billing_zip'] ?? ''),
            $cardName, $cardLast4, $cardBrand,
            $_SERVER['REMOTE_ADDR'] ?? 'sheets-sync'
        ]);

        $lid = (int)$db->lastInsertId();
        $fullName = trim("$paxFirst $paxLast");
        $db->prepare("INSERT INTO crm_notifications (type, message, lead_id, lead_table) VALUES (?,?,?,'booking_leads')")
           ->execute(['new_booking', "Sheet sync booking: {$fullName} | {$origin}→{$destination} | \${$price}", $lid]);

        echo json_encode(['ok' => true, 'action' => 'booking_lead', 'id' => $lid]);

    } else {
        echo json_encode(['ok' => false, 'error' => "Unknown action: '{$action}'. Use 'sheet1' or 'sheet2'."]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
