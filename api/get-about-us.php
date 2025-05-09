<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/controls/api/edit-post-control.php';

define("MAX_DELETE_AMOUNT", 1000);

if (!$db->connect())
    Utils\HTTPResponse::exitJsonExtended('internal-error', 'server-error', 'Errore interno, riprovare più tardi');

$conn = $db->getConnection();


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


handleFetchAboutUs();

//echo     json_encode($feeds);