<?php

$hlsStreamingEndpoint = "http://38.242.198.67/hls-content/BAyCE-DAB2w-AxxCZ-B6d54-4BECY.m3u8";

header("Cache-Control: no-cache");
header("Content-Type: application/json");

$content = @file_get_contents($hlsStreamingEndpoint);
if (empty($content) || is_null($content))
    exit(json_encode(["status" => "offline"]));


exit(json_encode(["status" => "online"]));
