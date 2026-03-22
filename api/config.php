<?php
// ============================================================
//  SUITDEM ERP — API Config
//  ⚠️  Ce fichier contient les credentials — ne pas publier en clair
//  Pour déployer sur LWS : changer uniquement les 5 lignes DB_*
// ============================================================


// MYSQL_ADDON_URI=mysql://uel1aury0zfhkuo2:gArDeOn4WG9vMwXYi8i6@bvsbhczi5dtypakwbpf4-mysql.services.clever-cloud.com:3306/bvsbhczi5dtypakwbpf4

// ── Environnement local MAMP ──────────────────────────────────
define('DB_HOST', 'bvsbhczi5dtypakwbpf4-mysql.services.clever-cloud.com');
define('DB_PORT', '3306');        // 8889 MAMP local | 3306 LWS production
define('DB_NAME', 'bvsbhczi5dtypakwbpf4');
define('DB_USER', 'uel1aury0zfhkuo2');
define('DB_PASS', 'gArDeOn4WG9vMwXYi8i6');

// ── Token de sécurité ─────────────────────────────────────────
// Même valeur dans l'app (window.DB._phpToken)
define('API_TOKEN', 'suitdem_secret_2025');

// ── Tables autorisées ─────────────────────────────────────────
define('ALLOWED_TABLES', ['clients','products','insurance','services',
                          'options','misc','vehicles','team','operations']);

// ── Connexion PDO partagée ────────────────────────────────────
function getDB(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT .
           ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

// ── Helpers ───────────────────────────────────────────────────
function jsonOk(mixed $data): void {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true, 'data' => $data]);
    exit;
}

function jsonErr(string $msg, int $code = 400): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

function checkToken(): void {
    $token = $_SERVER['HTTP_X_API_TOKEN']
          ?? $_POST['_token']
          ?? $_GET['_token']
          ?? '';
    if ($token !== API_TOKEN) jsonErr('Unauthorized', 401);
}

function checkTable(string $table): void {
    if (!in_array($table, ALLOWED_TABLES, true)) jsonErr('Table non autorisée');
}

// CORS — autorise l'app locale à appeler l'API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-API-Token');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
