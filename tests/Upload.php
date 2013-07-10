<?php

include dirname(__DIR__) . '/lib/Quandl.php';
$settings = include dirname(__DIR__) . '/settings.php';
$token = $settings->token;

$data = array(
	'2013-07-01' => 1,
	time() => 12.345,
);

$quandl = new Quandl($token);
$success = $quandl->upload('MY', 'My First Series', $data, FALSE,
	'My first data series upload for testing Quandl!');
var_dump($success);