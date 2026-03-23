<?php
// ============================================================
//  SUITDEM ERP — save_operation.php
//  Upsert une opération (INSERT ou UPDATE selon Dossier)
//  POST /api/save_operation.php  body: JSON row
// ============================================================
require __DIR__ . '/config.php';
checkToken();

$body = json_decode($GLOBALS['_raw_input'] ?? file_get_contents('php://input'), true);
if (!$body || empty($body['Dossier'])) jsonErr('Dossier manquant');

$db = getDB();

// Colonnes autorisées (correspondent exactement à App.saveOperation row)
$cols = ['Dossier','Date','Client','Tel','Email',
         'Adresse Dep','Adresse Arr','Volume','Categorie',
         'Total TTC','FULL_DATA_JSON'];

$data = [];
foreach ($cols as $col) {
    $data[$col] = $body[$col] ?? '';
}

// Build UPSERT (INSERT … ON DUPLICATE KEY UPDATE)
$colNames   = implode(', ', array_map(fn($c) => "`$c`", array_keys($data)));
$colPlaceholders = implode(', ', array_map(fn($c) => ":$c", array_map('sanitizeKey', array_keys($data))));
$updates    = implode(', ', array_map(
    fn($c) => "`$c` = VALUES(`$c`)",
    array_filter(array_keys($data), fn($c) => $c !== 'Dossier')
));

// Sanitize keys for PDO placeholders (remove spaces)

$paramData = [];
foreach ($data as $col => $val) {
    $paramData[':' . sanitizeKey($col)] = $val;
}

$sql = "INSERT INTO `operations` ($colNames) VALUES ($colPlaceholders)
        ON DUPLICATE KEY UPDATE $updates";

$stmt = $db->prepare($sql);
$stmt->execute($paramData);

jsonOk(['dossier' => $data['Dossier']]);
