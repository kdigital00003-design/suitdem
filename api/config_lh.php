<?php
// ============================================================
//  SUITDEM ERP — API Config v19.75
//  ⚠️  Ne pas publier en clair
//
//  DÉPLOIEMENT — changer uniquement ces 5 lignes :
//  DB_HOST  : fourni par l'hébergeur (panneau MySQL)
//  DB_PORT  : 3306 (LWS, InfinityFree, Clever Cloud...)
//  DB_NAME  : nom de la base créée dans phpMyAdmin
//  DB_USER  : utilisateur MySQL fourni par l'hébergeur
//  DB_PASS  : mot de passe MySQL
// ============================================================

// ── Environnement local MAMP ──────────────────────────────────
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '8889');   // MAMP=8889 | LWS/autres=3306
define('DB_NAME', 'suitdem');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// ── Token de sécurité (même valeur dans index.html : DB._phpToken) ──
define('API_TOKEN', 'suitdem_secret_2025');

// ── Tables autorisées ─────────────────────────────────────────
define('ALLOWED_TABLES', ['clients','products','insurance','services',
                          'options','misc','vehicles','team','operations']);

// ── Connexion PDO ─────────────────────────────────────────────
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

// sanitizeKey: converts column names to safe PDO placeholder keys
function sanitizeKey(string $k): string {
    return preg_replace('/[^a-zA-Z0-9_]/', '_', $k);
}

function checkToken(): void {
    // 1. Header (standard)
    $token = $_SERVER['HTTP_X_API_TOKEN'] ?? '';
    // 2. Body JSON (proxies that strip custom headers)
    if (!$token) {
        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true);
        $token = $body['_token'] ?? '';
        $GLOBALS['_raw_input'] = $raw; // store for re-use (php://input readable once)
    }
    // 3. Query string (GET requests)
    if (!$token) $token = $_GET['_token'] ?? '';

    if ($token !== API_TOKEN) jsonErr('Unauthorized', 401);
}

function checkTable(string $table): void {
    if (!in_array($table, ALLOWED_TABLES, true)) jsonErr('Table non autorisée');
}

// CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-API-Token');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
