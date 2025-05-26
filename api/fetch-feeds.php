<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/controls/api/fetch-feeds-control.php';


header("Content-type: application/json");

// fetch-feeds?type=news&c=40&p=1&query=

if ($_SERVER['REQUEST_METHOD'] !== "POST" && !FF_DEBUG_MODE)
    dieWithStatus("invalid_request_type");

if (!$db->connect())
    dieWithStatus("internal_error");


$token = null_default(@$_GET['tok'], "");
$type  = null_default(@$_GET['type'], null);
$mode  = null_default(@$_GET['mode'], "multi");
$items = intval(null_default(@$_GET['c'], ITEMS_PER_PAGE));
$page  = intval(null_default(@$_GET['p'], 1));
$query = null_default(@$_GET['query'], "");

if ($items > MAX_ITEMS)
    $items = MAX_ITEMS;


if (!isset($token) && HAS_TOKEN) {
    if (!verifyToken($token))
        dieWithStatus("invalid_token");
}


if (is_null($type))
    dieWithStatus("invalid_type");

$type = strtolower($type);

if (!in_array($type, $validFeedTypes))
    dieWithStatus("invalid_type");

$conn        = $db->getConnection();

if ($mode == "multi") {
    if ($page <= 0)
        $page = 1;

    $limitIndex   = ($page - 1) * $items;

    $search_query = "";
    $hasQuery     = false;

    if (strlen($query) > 0) {
        $hasQuery = true;
        $words = explode(" ", $query);

        $sanitized_words = array_map(function($word) use ($conn) {
            return $conn->quote("%" . $word . "%"); // Adding % for LIKE
        }, $words);

        $where_clause = implode(" OR ", array_map(function($word) {
            return "`title` LIKE $word";
        }, $sanitized_words));


        $search_query = " AND (" . $where_clause . ")";
    }

    $querySQL = "SELECT 
            `id`, `title`, `image_url`, `timestamp`
            FROM `feeds` 
            WHERE `type` = '$type'$search_query
            ORDER BY `timestamp` DESC
        ";

    $feeds_count = fetchFeedsCount($querySQL);

    if ($feeds_count < 0)
        dieWithStatus("internal_error_1");

    try {

        $stmt = $conn->prepare($querySQL . " LIMIT $limitIndex, $items");

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        exit(json_encode([
            "status"         => "success",
            "feeds_count"    => $feeds_count,
            "items_per_page" => $items,
            "feeds"          => $result
        ]));
    } catch (\PDOException $ex) {
        dieWithStatus("internal_error_2");
    }
} else if ($mode == "single") {
    $feedId = intval(@$_GET['id']);
    try {

        $stmt = $conn->prepare("SELECT * FROM `feeds` WHERE `id` = ?");

        $stmt->execute([$feedId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result)
            dieWithStatus("feed_not_found");

        exit(json_encode([
            "status"         => "success",
            "feed"           => $result,
        ]));
    } catch (\PDOException $ex) {
        dieWithStatus("internal_error_2");
    }
}
