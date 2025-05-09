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

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    Utils\HTTPResponse::exitJsonExtended('bad-request', 'server-error', 'Richiesta non valida');

if (!check_credentials())
    Utils\HTTPResponse::exitJsonExtended('not-authorized', 'server-error', 'Richiesta non valida');


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


function handleEditAboutUs() {
    global $conn;

    $input              = @file_get_contents('php://input');
    $input              = @json_decode($input, true);
    

    if (!$input)
        Utils\HTTPResponse::exitJsonExtended('bad-request-inputdata', 'server-error', 'Richiesta non valida');

    $content            = @$input['content'];
    
    if (is_null($content) || empty($content))
        $content = "";
    
    $stmt = $conn->prepare("UPDATE `site_configuration` SET `about_me` = ?");

    try {
        $result = $stmt->execute([$content]);

        if ($result) {
            exit(json_encode([
                'status'  => 'success'
            ]));
        } else {
            Utils\HTTPResponse::exitJsonExtended('internal-error-0', 'server-error', 'Modifica contatti fallita, riprova.');
        }
    } catch(\PDOException $ex) {
        //echo $ex->getMessage();

        Utils\HTTPResponse::exitJsonExtended('internal-error-1', 'server-error', 'Modifica contatti fallita, riprova.');       
    }
}

function handleFetchAboutUs() {
    global $conn;
    $stmt = $conn->prepare("SELECT `about_me` FROM `site_configuration`");
    

    try {
        $result = $stmt->execute();

        if ($result) {
            $content = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$content)
                Utils\HTTPResponse::exitJsonExtended('internal-error-0', 'server-error', 'Fetch contatti fallita, riprova.');
            
            $content = $content['about_me'];
            
            exit(json_encode([
                'status'  => 'success',
                'content' => $content
            ]));
        } else {
            Utils\HTTPResponse::exitJsonExtended('internal-error-1', 'server-error', 'Fetch contatti fallita, riprova.');
        }
    } catch(\PDOException $ex) {
        //echo $ex->getMessage();

        Utils\HTTPResponse::exitJsonExtended('internal-error-2', 'server-error', 'Fetch contatti fallita, riprova.');       
    }
}


$action = @$_GET['action'];

if (!in_array($action, ['edit', 'fetch']))
    Utils\HTTPResponse::exitJsonExtended('bad-request', 'server-error', 'Richiesta non valida');

switch($action)
{
    case 'edit':
        handleEditAboutUs();
        return;
    case 'fetch':
        handleFetchAboutUs();
        return;
    default:
        Utils\HTTPResponse::exitJsonExtended('bad-request-1', 'server-error', 'Richiesta non valida');
        return;
}

//echo     json_encode($feeds);