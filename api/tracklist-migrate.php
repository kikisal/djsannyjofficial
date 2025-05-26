<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/autoload.php';

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

if (!$db->connect())
    Utils\HTTPResponse::exitJson('SERVER_ERROR', SERVER_ERROR_CODE);

$conn = $db->getConnection();

// function generateToken($length = 32) {
//     return bin2hex(random_bytes($length));
// }

// $feedIds = $conn->prepare('SELECT `feed_id` FROM `tracklist` GROUP BY `feed_id`;');
// $feedIds->execute();
// $feedIds = $feedIds->fetchAll(PDO::FETCH_ASSOC);

// $updateStmt      = $conn->prepare('UPDATE `feeds` SET `tracklist_token` = ? WHERE `id` = ?');
// $tracklistStmt   = $conn->prepare('SELECT * FROM `tracklist` WHERE `feed_id` = ?');
// $insertTrackStmt = $conn->prepare('INSERT INTO `tracklist_editor` (`token`, `title`, `audio_url`, `duration`, `timestamp`) VALUES (?, ?, ?, ?, ?)');

// foreach ($feedIds as $feedId) {
//     $feedId = $feedId['feed_id'];
    
//     $token = generateToken();
//     $tracks = $tracklistStmt->execute([$feedId]);

//     if (!$tracks) continue;
//     $tracks = $tracklistStmt->fetchAll(PDO::FETCH_ASSOC);
    
//     if (!$tracks || count($tracks) < 1)
//         continue;

//     $updateStmt->execute([$token, $feedId]);

//     foreach ($tracks as $track) {
//         $title     = $track['title'];
//         $audio_url = $track['audio_url'];
//         $duration  = $track['duration'];
//         $time      = time();

//         $insertTrackStmt->execute([
//             $token,
//             $title,
//             $audio_url,
//             $duration,
//             $time
//         ]);
//     }
// }

// $buffer = '';

// $feeds = $conn->prepare("SELECT * FROM `feeds` WHERE `tracklist_token` != ''");
// $feeds->execute();
// $feeds = $feeds->fetchAll(PDO::FETCH_ASSOC);

// foreach ($feeds as $feed) {
//     $buffer .= "UPDATE `feeds` SET `tracklist_token` = '" . $feed['tracklist_token'] . "' WHERE `id` = " . $feed['id'] . ";\n";
// }

// echo $buffer;
