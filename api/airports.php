<?php
/* ==========================================================
   api/airports.php — Complete 7,917 World Airports Search
   Powered by github.com/mwgg/Airports dataset + Ignav API
   ========================================================== */
require_once __DIR__ . '/../includes/config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$q     = strtolower(trim($_GET['q'] ?? ''));
$limit = min((int)($_GET['limit'] ?? 10), 30);

if (strlen($q) < 1) { echo '[]'; exit; }

$dataFile = __DIR__ . '/../assets/data/airports.json';
$allAirports = [];
if (file_exists($dataFile)) {
    $allAirports = json_decode(file_get_contents($dataFile), true) ?: [];
}

// 1. Exact code matches first, then prefix matches, then substring matches
$exactMatches = [];
$prefixMatches = [];
$substringMatches = [];

foreach ($allAirports as $a) {
    $code = strtolower($a['code'] ?? '');
    $city = strtolower($a['city'] ?? '');
    $name = strtolower($a['name'] ?? '');
    $country = strtolower($a['country'] ?? '');

    if ($code === $q) {
        $exactMatches[] = $a;
    } elseif (strpos($code, $q) === 0 || strpos($city, $q) === 0) {
        $prefixMatches[] = $a;
    } elseif (strpos($code, $q) !== false || strpos($city, $q) !== false || strpos($name, $q) !== false || strpos($country, $q) !== false) {
        $substringMatches[] = $a;
    }

    if (count($exactMatches) + count($prefixMatches) + count($substringMatches) >= $limit * 3) {
        break;
    }
}

$localMatches = array_merge($exactMatches, $prefixMatches, $substringMatches);

// 2. Ignav live API fallback/merge
$liveAirports = [];
$url  = IGNAV_BASE . '/api/airports?q=' . urlencode($q) . '&limit=' . $limit;
$opts = ['http' => [
    'method'  => 'GET',
    'header'  => 'X-Api-Key: ' . IGNAV_API_KEY . "\r\nUser-Agent: SkyFare/1.0\r\n",
    'timeout' => 3,
    'ignore_errors' => true,
]];
$result = @file_get_contents($url, false, stream_context_create($opts));
if ($result !== false) {
    $decoded = json_decode($result, true);
    if (is_array($decoded) && count($decoded) > 0) {
        $liveAirports = $decoded;
    }
}

// Merge live + local (deduplicate by IATA code)
$merged = [];
$seenCodes = [];

foreach (array_merge($liveAirports, $localMatches) as $item) {
    $code = strtoupper($item['code'] ?? '');
    if ($code && !isset($seenCodes[$code])) {
        $seenCodes[$code] = true;
        $merged[] = [
            'code'    => $code,
            'name'    => $item['name'] ?? $code,
            'city'    => $item['city'] ?? $code,
            'country' => $item['country'] ?? '',
        ];
    }
    if (count($merged) >= $limit) break;
}

echo json_encode($merged);
