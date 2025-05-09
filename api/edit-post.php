<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/controls/api/edit-post-control.php';

define("MAX_DELETE_AMOUNT", 1000);

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


if (!check_credentials())
    Utils\HTTPResponse::exitJsonExtended('not-authorized', 'server-error', 'Richiesta non valida');

    
function handleDeletePost() {
    // Utils\HTTPResponse::exitJsonExtended('not-implemented', 'success', 'Not implemented');
    global $conn;

    $input              = @file_get_contents('php://input');
    $input              = @json_decode($input, true);

    if (!$input)
        Utils\HTTPResponse::exitJsonExtended('bad-request-inputdata', 'server-error', 'Richiesta non valida');

    $feeds = $input['feedIds'];
    
    if (!is_array($feeds))
        Utils\HTTPResponse::exitJsonExtended('bad-request-inputdata-1', 'server-error', 'Richiesta non valida');

    $placeholders = implode(',', array_fill(0, count($feeds), '?'));


    try {
        $stmt = $conn->prepare("DELETE FROM `feeds` WHERE `id` IN ($placeholders)");

        for ($i = 0; $i < count($feeds); ++$i) {
            if ($i >= MAX_DELETE_AMOUNT)
                break;

            if (gettype($feeds[$i]) !== 'integer')
                continue;

            $feed = $feeds[$i];
            $stmt->bindValue($i + 1, $feed, PDO::PARAM_INT);
        }


        if(!$stmt->execute())
            Utils\HTTPResponse::exitJsonExtended('internal-error-0', 'server-error', 'Elminazione del feed fallito, riprova.');

        exit(json_encode([
            'status'  => 'success'
        ]));

    } catch(\PDOException $ex) {
        Utils\HTTPResponse::exitJsonExtended('internal-error-1', 'server-error', 'Elminazione del feed fallito, riprova.');       
    }
}

function handleCreatePost() {
    global $conn;

    $input              = @file_get_contents('php://input');
    $input              = @json_decode($input, true);

    if (!$input)
        Utils\HTTPResponse::exitJsonExtended('bad-request-inputdata', 'server-error', 'Richiesta non valida');

    $title              = @$input['title'];
    $targetSection      = @$input['targetSection'];
    $textContent        = @$input['textContent'];
    $youtubeURL         = @$input['youtubeURL'];
    $imageCoverUrl      = @$input['imageCoverUrl'];
    $playerCoverImage   = @$input['playerCoverImage'];
    $audioCoverUrl      = @$input['audioCoverUrl'];
    $audioTitle         = @$input['audioTitle'];
    $videoFileUrl       = @$input['videoFileUrl'];

    if (empty($title))
        Utils\HTTPResponse::exitJsonExtended('invalid-data', 'server-error', 'Inserire un titolo più lungo');

    if (!in_array($targetSection, ['news', 'podcast', 'programs']))
        Utils\HTTPResponse::exitJsonExtended('invalid-data', 'server-error', 'La sezione di destinazione non esiste.');

    if (empty($imageCoverUrl))
        Utils\HTTPResponse::exitJsonExtended('invalid-data', 'server-error', 'Inserire la copertina.');

    $stmt = $conn->prepare('INSERT INTO `feeds` (
    `type`, `timestamp`, `image_url`, `audio_url`, `video_urls`, 
    `title`, `text_content`, `author`, `audio_title`, `audio_cover_url`,
    `video_file_url`) VALUES (?, ?, ?, ?, ?, ?, ?, \'\', ?, ?, ?);');

    try {
        $result = $stmt->execute([$targetSection, time(), $imageCoverUrl, $audioCoverUrl, $youtubeURL, $title, $textContent, $audioTitle, $playerCoverImage, $videoFileUrl]);
        $feedId = $conn->lastInsertId();

        if ($result) {
            exit(json_encode([
                'status'  => 'success',
                'feedId'  => $conn->lastInsertId(),
                'type'    => $targetSection,
                'feedUrl' => SITE_URL . '/view-post?id=' . $feedId . '&type=' . $targetSection
            ]));
        } else {
            Utils\HTTPResponse::exitJsonExtended('internal-error', 'server-error', 'Caricamento del feed fallito, riprova.');
        }
    } catch(\PDOException $ex) {
        Utils\HTTPResponse::exitJsonExtended('internal-error', 'server-error', 'Caricamento del feed fallito, riprova.');       
    }
}


function handleEditPost() {
    global $conn;

    $input              = @file_get_contents('php://input');
    $input              = @json_decode($input, true);

    if (!$input)
        Utils\HTTPResponse::exitJsonExtended('bad-request-inputdata', 'server-error', 'Richiesta non valida');

    $feedId             = intval(@$_GET['id']);
    $title              = @$input['title'];
    $targetSection      = @$input['targetSection'];
    $textContent        = @$input['textContent'];
    $youtubeURL         = @$input['youtubeURL'];
    $imageCoverUrl      = @$input['imageCoverUrl'];
    $playerCoverImage   = @$input['playerCoverImage'];
    $audioCoverUrl      = @$input['audioCoverUrl'];
    $audioTitle         = @$input['audioTitle'];
    $videoFileUrl       = @$input['videoFileUrl'];

    
    if (!@isset($_GET['id']) || $feedId < 0)
        Utils\HTTPResponse::exitJsonExtended('bad-request-inputdata-1', 'server-error', 'Richiesta non valida');

    if (empty($title))
        Utils\HTTPResponse::exitJsonExtended('invalid-data', 'server-error', 'Inserire un titolo più lungo');

    if (!in_array($targetSection, ['news', 'podcast', 'programs']))
        Utils\HTTPResponse::exitJsonExtended('invalid-data', 'server-error', 'La sezione di destinazione non esiste.');

    if (empty($imageCoverUrl))
        Utils\HTTPResponse::exitJsonExtended('invalid-data', 'server-error', 'Inserire la copertina.');
    
    $stmt = $conn->prepare("UPDATE `feeds` SET 
        `type` = ?,
        `image_url` = ?, 
        `audio_url` = ?, 
        `video_urls` = ?, 
        `title` = ?, 
        `text_content` = ?,  
        `audio_title` = ?, 
        `audio_cover_url` = ?, 
        `video_file_url` = ? WHERE `id` = ?"
    );

    try {
        $result = $stmt->execute([$targetSection, $imageCoverUrl, $audioCoverUrl, $youtubeURL, $title, $textContent, $audioTitle, $playerCoverImage, $videoFileUrl, $feedId]);

        if ($result) {
            exit(json_encode([
                'status'  => 'success',
                'feedId'  => $feedId,
                'type'    => $targetSection,
                'feedUrl' => SITE_URL . '/view-post?id=' . $feedId . '&type=' . $targetSection
            ]));
        } else {
            Utils\HTTPResponse::exitJsonExtended('internal-error', 'server-error', 'Modifica del feed fallito, riprova.');
        }
    } catch(\PDOException $ex) {
        //echo $ex->getMessage();

        Utils\HTTPResponse::exitJsonExtended('internal-error', 'server-error', 'Modifica del feed fallito, riprova.');       
    }
}

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

/* enable this at the end
if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    Utils\HTTPResponse::exitJsonExtended('bad-request', 'server-error', 'Richiesta non valida');
*/

$action = @$_GET['action'];

if (!in_array($action, ['create', 'edit', 'delete']))
    Utils\HTTPResponse::exitJsonExtended('bad-request', 'server-error', 'Richiesta non valida');

switch($action)
{
    case 'create':
        handleCreatePost();
        return;
    case 'edit':
        handleEditPost();
        return;
    case 'delete':
        handleDeletePost();
        return;
    default:
        Utils\HTTPResponse::exitJsonExtended('bad-request-1', 'server-error', 'Richiesta non valida');
        return;
}

//echo     json_encode($feeds);