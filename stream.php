<?php
$targetServer = "http://78.129.132.7:7708/;";

try {
    echo @file_get_contents($targetServer);

} catch(\Exception $ex) {
    echo $ex->getMessage();
}