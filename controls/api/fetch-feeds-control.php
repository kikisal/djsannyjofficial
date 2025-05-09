<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/autoload.php';


define("FF_DEBUG_MODE", false);
define("HAS_TOKEN", false);
define("ITEMS_PER_PAGE", 40);
// return 200 max items per page.
define("MAX_ITEMS", 200);

function dieWithStatus($message) {
    exit(json_encode([
        "status"    => "server_error",
        "message"   => $message 
    ]));
}

function verifyToken($token) {
    return true; // to implement
}

function null_default($val, $def) {
    if (!isset($val) || is_null($val) || empty($val))
        return $def;
    return $val; 
}

$validFeedTypes = [
    "news",
    "podcast",
    "programs"
];


use Database\PDOConnConfig as PDOConfig;
use Database\MySQLPDODatabase as MysqlDatabase;

$db = new MysqlDatabase(
    PDOConfig::create()
    ->host(DB_HOST)
    ->db(DB_DATABASE)
    ->user(DB_USER)
    ->pass(DB_PASS)
    ->charset(DB_CHARSET)
    ->options([
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ])
);

function fetchFeedsCount($query) {
    global $db;
    $conn = $db->getConnection();
    if (!$conn)
        return -1;

    try {
        $stmt = $conn->prepare("SELECT COUNT(*) as feeds_count FROM ($query) as `ftable`");
        if (!$stmt->execute())
            return -1;
        
        return $stmt->fetch(PDO::FETCH_ASSOC)["feeds_count"];
    } catch(\PDOException $ex) {
        return -1;
    }
}