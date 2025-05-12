<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

function cv() {
    global $cacheCounter;
    if (DISABLE_GLOBAL_CACHE)
        return $cacheCounter;
    return time();
}

function get_contents($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $output = curl_exec($ch);

    if ($output === false)
        return null;

    curl_close($ch);

    return $output;
}

function fetchRadioLink() {
    try {
        $contents = @get_contents(RADIO_STREAMING_WEBPAGE);
        
        if (!$contents)
            return null;

        $dom = new DOMDocument();
        @$dom->loadHTML($contents);
        
        $urlAddr = $dom->getElementById('urladdress');
    
        return trim($urlAddr->textContent);
    } catch (Exception $ex) {
        return null;
    }
}

function _or($a, $b) {
    return $a != null ? $a : $b;
}

function stripURLQuery($uri) {
    return explode("?", $uri)[0];
}

function routeName() {
    global $uri, $routes;
    return isset($routes[$uri]) ? $routes[$uri]["name"] : "Senza titolo";
}

$radioLink = _or(fetchRadioLink(), RADIO_STREAM_URL);

$routes    = require_once $_SERVER['DOCUMENT_ROOT'] . '/routes.php';
$uri       = stripURLQuery($_SERVER['REQUEST_URI']);

require_once $_SERVER['DOCUMENT_ROOT'] . '/autoload.php';
require_once __DIR__ . "/db-common.php";