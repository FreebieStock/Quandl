<?php

include dirname(__DIR__) . '/lib/Quandl.php';
$settings = include dirname(__DIR__) . '/settings.php';
$token = $settings->token;

$quandl = new Quandl($token);
$data = $quandl->search('oil');
$data = json_decode($data);
var_dump($data);
