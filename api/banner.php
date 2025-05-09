<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/controls/api/banner-control.php';

define("MAX_DELETE_AMOUNT", 1000);
define("MAX_BANNER_UPLOADING", 30);

function getUserByName($name) {
    global $users;
    foreach($users as $user) {
        if ($user['username'] == $name)
            return $user;
    }

    return null;
}

function userHasPermissions($user) {
    global $authorizedUsers;
    return in_array($user, $authorizedUsers);
}

function check_credentials(): bool {
    
    $body = file_get_contents('php://input');
    $body = @json_decode($body, true);
    if (!$body)
        return false;
    
    if (!array_key_exists('auth', $body))
        return false;


    $credentials = $body['auth'];

    $decodedCredentials = base64_decode($credentials);

    $userPass = explode(':', $decodedCredentials, 2);

    if (count($userPass) < 2)
        return false;

    $username  = $userPass[0];
    $password  = $userPass[1];

    $user      = getUserByName($username);
    
    if (!$user)
        return false;

    if ($user['password'] !== $password)
        return false;

    if (!userHasPermissions($username))
        return false;

    return true;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    Utils\HTTPResponse::exitJsonExtended('bad-request', 'server-error', 'Richiesta non valida');

if (!$db->connect())
    Utils\HTTPResponse::exitJsonExtended('internal-error', 'server-error', 'Errore interno, riprovare più tardi');

$conn = $db->getConnection();

$users = [
    [
        'username' => 'sanny',
        'password' => 'cfcaf9383c250f48a1a1b79ad0c7f6ba0d4aba51'
    ]
];

$authorizedUsers = ['sanny'];

$bannerLimit = 50; // max 50 images fetchable.

if (!check_credentials())
    Utils\HTTPResponse::exitJsonExtended('not-authorized', 'server-error', 'Richiesta non valida');

function getCurrentBannerCount() {
    global $conn;

    $stmt = $conn->prepare("SELECT COUNT(*) as `count` FROM `banner_images`");
    if (!$stmt->execute())
        return -1;

    return $stmt->fetch(PDO::FETCH_ASSOC)["count"];
}

function handleFetchRequest($input) {
    global $conn, $bannerLimit;

    try {
        $stmt = $conn->prepare("SELECT * FROM `banner_images`  ORDER BY `order` ASC LIMIT $bannerLimit");
        
        if (!$stmt->execute())
            Utils\HTTPResponse::exitJsonExtended('internal-error-0', 'server-error', 'Errore interno');

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        exit(json_encode([
            "status"  => "success",
            "banners" => $data
        ]));

    } catch(\PDOException $ex) {
        Utils\HTTPResponse::exitJsonExtended('internal-error-0', 'server-error', 'Errore interno');
    }
}

function handleAddRequest($input) {
    global $conn;

    $currentBannerCount = getCurrentBannerCount();

    if ($currentBannerCount < 0)
        Utils\HTTPResponse::exitJsonExtended('internal-error-1', 'server-error', 'Errore interno');

    if ($currentBannerCount >= MAX_BANNER_UPLOADING) {
        exit (json_encode([
            "status"          => "server-error",
            "message"         => "limit-exceeded",
            "extendedMessage" => "Non puoi aggiungere più di " . MAX_BANNER_UPLOADING . " banner.",
            "limit"           => MAX_BANNER_UPLOADING
        ]));
    }

    try {
        $stmt = $conn->prepare(
            "INSERT INTO `banner_images` (`image_url`, `order`)
            SELECT \"\", COALESCE(MAX(`order`), 0) + 1 FROM `banner_images`;"        
        );
        
        if (!$stmt->execute())
            Utils\HTTPResponse::exitJsonExtended('internal-error-0', 'server-error', 'Errore interno');

        exit(json_encode([
            "status"  => "success",
            "banner" => [
                "id"        => $conn->lastInsertId(),
                "image_url" => ''
            ]
        ]));

    } catch(\PDOException $ex) {
        Utils\HTTPResponse::exitJsonExtended('internal-error-2', 'server-error', 'Errore interno');
    }
    
}


function handleEditOrderRequest($input) {
    global $conn;

    if (!array_key_exists("ordering", $input))
        Utils\HTTPResponse::exitJsonExtended('invalid-data-format-0', 'server-error', 'Errore interno');

    $ordering = $input["ordering"];

    if (!is_array($ordering))
        Utils\HTTPResponse::exitJsonExtended('invalid-data-format-1', 'server-error', 'Errore interno');

    $cases = "";
    $ids   = "(";
    
    foreach ($ordering as $order) {
        if (!array_key_exists("card_id", $order) || !array_key_exists("order", $order))
            Utils\HTTPResponse::exitJsonExtended('invalid-data-format-1', 'server-error', 'Errore interno');
        
        $order["card_id"] = intval($order["card_id"]);
        $order["order"]   = intval($order["order"]);

        $cases .= "WHEN `id` = " . $order["card_id"] . " THEN " . $order["order"] . " ";
        $ids   .= $order["card_id"] . ", ";
    }

    $cases .= "END";
    $ids    = substr($ids, 0, strlen($ids) - 2);
    $ids   .= ")";
    
    $query = "UPDATE `banner_images` SET `order` = CASE $cases WHERE `id` IN $ids";
    
    try {
        $stmt = $conn->prepare($query);
        
        if (!$stmt->execute())
            Utils\HTTPResponse::exitJsonExtended('internal-error-0', 'server-error', 'Errore interno');

        exit(json_encode([
            "status"  => "success"
        ]));

    } catch(\PDOException $ex) {
        Utils\HTTPResponse::exitJsonExtended('internal-error-2', 'server-error', 'Errore interno');
    }
}

function handleEditRequest($input) {
    global $conn;


    if (!array_key_exists("card", $input))
        Utils\HTTPResponse::exitJsonExtended('invalid-data-format-0', 'server-error', 'Errore interno');

    $card = $input["card"];
    
    if (!array_key_exists("id", $card))
        Utils\HTTPResponse::exitJsonExtended('invalid-data-format-1', 'server-error', 'Errore interno');

    if (!array_key_exists("image_url", $card))
        Utils\HTTPResponse::exitJsonExtended('invalid-data-format-2', 'server-error', 'Errore interno');

    $card["id"] = intval($card["id"]);

    try {
        $stmt = $conn->prepare("UPDATE `banner_images` SET `image_url` = ? WHERE `id` = ?");
        
        if (!$stmt->execute([$card['image_url'], $card['id']]))
            Utils\HTTPResponse::exitJsonExtended('internal-error-0', 'server-error', 'Errore interno');

        exit(json_encode([
            "status"  => "success"
        ]));

    } catch(\PDOException $ex) {
        Utils\HTTPResponse::exitJsonExtended('internal-error-2', 'server-error', 'Errore interno');
    }

}

function handleDeleteRequest($input) {
    global $conn;


    if (!array_key_exists("card", $input))
        Utils\HTTPResponse::exitJsonExtended('invalid-data-format-0', 'server-error', 'Errore interno');

    $card = $input["card"];
    
    if (!array_key_exists("id", $card))
        Utils\HTTPResponse::exitJsonExtended('invalid-data-format-1', 'server-error', 'Errore interno');

    $card["id"] = intval($card["id"]);

    try {
        $stmt = $conn->prepare("DELETE FROM `banner_images` WHERE `id` = ?");
        
        if (!$stmt->execute([$card['id']]))
            Utils\HTTPResponse::exitJsonExtended('internal-error-0', 'server-error', 'Errore interno');

        exit(json_encode([
            "status"  => "success"
        ]));

    } catch(\PDOException $ex) {
        Utils\HTTPResponse::exitJsonExtended('internal-error-1', 'server-error', 'Errore interno');
    }

}

function handleBannerRequest() {
    $input              = @file_get_contents('php://input');
    $input              = @json_decode($input, true);

    if (!$input)
        Utils\HTTPResponse::exitJsonExtended('bad-request-inputdata', 'server-error', 'Richiesta non valida');

    $action = $input['action'];

    if (!in_array($action, ['fetch', 'add', 'delete', 'edit', 'edit-order']))
        Utils\HTTPResponse::exitJsonExtended('bad-request', 'server-error', 'Richiesta non valida');
    

    switch($action) {
        case 'fetch':
            handleFetchRequest($input);
            return;
        case 'edit':
            handleEditRequest($input);
            return;
        case 'edit-order':
            handleEditOrderRequest($input);
            return;
        case 'add':
            handleAddRequest($input);
        case 'delete':
            handleDeleteRequest($input);
    }
}

handleBannerRequest();