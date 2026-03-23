<?php
// ============================================================
//  SUITDEM ERP — save_row.php
//  Insert ou update une ligne dans n'importe quelle table
//  POST /api/save_row.php  body: { table, row, id? }
// ============================================================
require __DIR__ . '/config.php';
checkToken();

$body = json_decode($GLOBALS['_raw_input'] ?? file_get_contents('php://input'), true);
if (!$body || empty($body['table']) || empty($body['row'])) {
    jsonErr('Paramètres manquants: table, row');
}

$table = $body['table'];
$row   = $body['row'];
$id    = $body['id'] ?? null;

checkTable($table);

// Remove internal auto-increment id from row data if present
unset($row['id']);

$db = getDB();


if ($id !== null) {
    // UPDATE
    $setParts = implode(', ', array_map(fn($c) => "`$c` = :" . sanitizeKey($c), array_keys($row)));
    $paramData = [];
    foreach ($row as $col => $val) {
        $paramData[':' . sanitizeKey($col)] = $val;
    }
    $paramData[':id'] = $id;
    $stmt = $db->prepare("UPDATE `$table` SET $setParts WHERE id = :id");
    $stmt->execute($paramData);
    jsonOk(['updated' => $id]);
} else {
    // INSERT
    $colNames        = implode(', ', array_map(fn($c) => "`$c`", array_keys($row)));
    $colPlaceholders = implode(', ', array_map(fn($c) => ':' . sanitizeKey($c), array_keys($row)));
    $paramData = [];
    foreach ($row as $col => $val) {
        $paramData[':' . sanitizeKey($col)] = $val;
    }
    $stmt = $db->prepare("INSERT INTO `$table` ($colNames) VALUES ($colPlaceholders)");
    $stmt->execute($paramData);
    jsonOk(['inserted_id' => $db->lastInsertId()]);
}
