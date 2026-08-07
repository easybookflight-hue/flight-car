<?php
/* ==========================================================
   api/search.php — Ignav flight fares proxy
   Supports POST (JSON) and GET (query params)
   Strictly sanitizes payload to only send allowed keys to Ignav API
   ========================================================== */
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { 
    http_response_code(200); 
    exit; 
}

// Read body from POST or GET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?: [];
} else {
    $body = [
        'origin'         => $_GET['origin'] ?? $_GET['from'] ?? 'DEL',
        'destination'    => $_GET['destination'] ?? $_GET['to'] ?? 'BOM',
        'departure_date' => $_GET['departure_date'] ?? $_GET['dep'] ?? date('Y-m-d', strtotime('+14 days')),
        'return_date'    => $_GET['return_date'] ?? $_GET['ret'] ?? '',
        'adults'         => (int)($_GET['adults'] ?? 1),
        'cabin_class'    => $_GET['cabin_class'] ?? $_GET['cabin'] ?? 'economy',
        'trip_type'      => $_GET['trip_type'] ?? $_GET['trip'] ?? 'one-way',
    ];
}

$origin = strtoupper(trim($body['origin'] ?? $_GET['from'] ?? 'DEL'));
$dest   = strtoupper(trim($body['destination'] ?? $_GET['to'] ?? 'BOM'));
$dep    = trim($body['departure_date'] ?? $_GET['dep'] ?? date('Y-m-d', strtotime('+14 days')));
$ret    = trim($body['return_date'] ?? $_GET['ret'] ?? '');
$adults = (int)($body['adults'] ?? 1);
$cabin  = trim($body['cabin_class'] ?? 'economy');
$trip   = trim($body['trip_type'] ?? $_GET['trip'] ?? 'one-way');

// Map common city metro codes to main international airport IATA codes
$metroMap = [
    'TYO' => 'NRT',
    'LON' => 'LHR',
    'NYC' => 'JFK',
    'PAR' => 'CDG',
    'ROM' => 'FCO',
    'SEL' => 'ICN',
    'BJS' => 'PEK',
    'OSA' => 'KIX',
    'YTO' => 'YYZ',
    'MIL' => 'MXP',
    'BER' => 'BER',
    'MOW' => 'SVO',
    'WAS' => 'IAD',
    'CHI' => 'ORD',
    'RIO' => 'GIG',
    'SAO' => 'GRU',
    'BUA' => 'EZE',
    'BKK' => 'BKK',
];
if (isset($metroMap[$origin])) $origin = $metroMap[$origin];
if (isset($metroMap[$dest]))   $dest   = $metroMap[$dest];

if (strlen($origin) < 3) $origin = 'DEL';
if (strlen($dest) < 3)   $dest   = 'BOM';
if (empty($dep))         $dep    = date('Y-m-d', strtotime('+14 days'));
if ($adults < 1)         $adults = 1;
if (empty($cabin))       $cabin  = 'economy';

// Build STRICT payload (Ignav rejects any extra keys like trip_type, from_name, etc.)
$payloadArr = [
    'origin'         => $origin,
    'destination'    => $dest,
    'departure_date' => $dep,
    'adults'         => $adults,
    'cabin_class'    => $cabin,
    'market'         => MARKET
];

$isRoundTrip = ($trip === 'round-trip' || !empty($ret));
if ($isRoundTrip && !empty($ret)) {
    $payloadArr['return_date'] = $ret;
}

$endpoint = ($isRoundTrip && !empty($ret)) ? '/api/fares/round-trip' : '/api/fares/one-way';
$url      = IGNAV_BASE . $endpoint;
$payload  = json_encode($payloadArr);

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
$code   = 200;
foreach ($http_response_header ?? [] as $h) {
    if (preg_match('#HTTP/\S+\s+(\d+)#', $h, $m)) $code = (int)$m[1];
}

http_response_code($code ?: 200);
echo $result !== false ? $result : '{"error":"Upstream error"}';

// ── Save Search Lead to CRM DB (silent fail if DB not ready) ──
try {
    $crmCfg = __DIR__ . '/../crm/includes/config.php';
    $crmDb  = __DIR__ . '/../crm/includes/db.php';
    if (file_exists($crmCfg) && file_exists($crmDb)) {
        if (!defined('CRM_EMAIL')) require_once $crmCfg;
        require_once $crmDb;
        $db2 = getDB();
        $originName = $body['origin_name'] ?? $body['from_name'] ?? $origin;
        $destName   = $body['destination_name'] ?? $body['to_name'] ?? $dest;
        $custName   = trim($body['customer_name'] ?? $body['name'] ?? '');
        $phone      = trim($body['phone'] ?? '');
        $db2->prepare("INSERT INTO search_leads (origin,destination,origin_name,dest_name,dep_date,return_date,trip_type,passengers,cabin,customer_name,phone,ip_address,is_new) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,1)")
            ->execute([$origin,$dest,$originName,$destName,$dep?:null,($isRoundTrip?($ret?:null):null),$isRoundTrip?'roundtrip':'oneway',$adults,$cabin,$custName,$phone,$_SERVER['REMOTE_ADDR']??'']);
        $lid = (int)$db2->lastInsertId();
        $db2->prepare("INSERT INTO crm_notifications(type,message,lead_id,lead_table) VALUES(?,?,?,'search_leads')")
            ->execute(['new_search',"New search: {$origin}→{$dest} | {$adults}pax | {$cabin}",$lid]);
    }
} catch (Exception $ignored) {}
