<?php
// ============================================================
//  SUITDEM ERP — get.php
//  Charge toutes les tables et les retourne en JSON
//  GET /api/get.php  (header X-API-Token requis)
// ============================================================
require __DIR__ . '/config.php';
checkToken();

$db  = getDB();
$raw = [];

foreach (ALLOWED_TABLES as $table) {
    $raw[$table] = $db->query("SELECT * FROM `$table`")->fetchAll();
}

jsonOk($raw);
