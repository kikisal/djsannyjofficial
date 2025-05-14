<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/core.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/autoload.php';

header("Location: " . SITE_URL . "/release/" . @$_GET['id']);