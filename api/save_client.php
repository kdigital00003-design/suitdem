<?php
// ============================================================
//  SUITDEM ERP — save_client.php
//  Upsert un client (INSERT ou UPDATE selon Code client)
//  POST /api/save_client.php  body: JSON row
// ============================================================
require __DIR__ . '/config.php';
checkToken();

$body = json_decode(file_get_contents('php://input'), true);
if (!$body || empty($body['Client'])) jsonErr('Nom client manquant');

$db = getDB();

$cols = ['Code client','Client','Contact','Telephone','Email','Adresse'];
$data = [];
foreach ($cols as $col) {
    $data[$col] = $body[$col] ?? '';
}

function sanitizeKey(string $k): string {
    return preg_replace('/[^a-zA-Z0-9_]/', '_', $k);
}

$colNames        = implode(', ', array_map(fn($c) => "`$c`", array_keys($data)));
$colPlaceholders = implode(', ', array_map(fn($c) => ':' . sanitizeKey($c), array_keys($data)));
$updates         = implode(', ', array_map(
    fn($c) => "`$c` = VALUES(`$c`)",
    array_filter(array_keys($data), fn($c) => $c !== 'Code client')
));

$paramData = [];
foreach ($data as $col => $val) {
    $paramData[':' . sanitizeKey($col)] = $val;
}

$sql = "INSERT INTO `clients` ($colNames) VALUES ($colPlaceholders)
        ON DUPLICATE KEY UPDATE $updates";

$stmt = $db->prepare($sql);
$stmt->execute($paramData);

jsonOk(['code' => $data['Code client']]);
