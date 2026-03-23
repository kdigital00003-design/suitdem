<?php
// ============================================================
//  SUITDEM ERP — delete_row.php
//  Supprime une ligne par id dans une table donnée
//  POST /api/delete_row.php  body: { table, id }
// ============================================================
require __DIR__ . '/config.php';
checkToken();

$body = json_decode($GLOBALS['_raw_input'] ?? file_get_contents('php://input'), true);
if (!$body || empty($body['table']) || !isset($body['id'])) {
    jsonErr('Paramètres manquants: table, id');
}

$table = $body['table'];
$id    = $body['id'];

checkTable($table);

$db   = getDB();
$stmt = $db->prepare("DELETE FROM `$table` WHERE id = :id");
$stmt->execute([':id' => $id]);

jsonOk(['deleted' => $id]);
