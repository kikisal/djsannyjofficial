<?php

$hlsStreamingEndpoint = "http://38.242.198.67/hls-content/";

$fileName  = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_FILENAME);
$extension = pathinfo($_SERVER['REQUEST_URI'], PATHINFO_EXTENSION);

header("Cache-Control: no-cache");

if ($extension == "ts")
    header("Content-Type: video/mp2t");
else if($extension == "m3u8")
    header("Content-Type: application/vnd.apple.mpegurl");
else {
    header("HTTP/1.0 404 Not Found");
    exit;
}

echo @file_get_contents($hlsStreamingEndpoint . $fileName . "." . $extension);
exit;