<?php

include dirname(__DIR__) . '/lib/Quandl.php';
$settings = include dirname(__DIR__) . '/settings.php';
$token = $settings->token;

$code = 'STATCHINA/P1642';
$quandl = new Quandl($token);
$data = $quandl->download($code);
$data = json_decode($data);
var_dump($data);
