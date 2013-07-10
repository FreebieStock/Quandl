<?php

include dirname(__DIR__) . '/lib/Quandl.php';
$settings = include dirname(__DIR__) . '/settings.php';
$token = $settings->token;

$code = 'DOE/RWTC';
$quandl = new Quandl($token);
$data = $quandl->download($code, array(
	Quandl::PARAM_TRANSFORM => Quandl::TRANSFORM_DIFF,
	Quandl::PARAM_COLLAPSE => Quandl::COLLAPSE_ANNUAL,
	Quandl::PARAM_END_DATE => Quandl::timestampToDate(time()),
	Quandl::PARAM_START_DATE => '2008-01-01',
	Quandl::PARAM_ROWS => 5,
	Quandl::PARAM_SORT => Quandl::SORT_DESC,
));
$data = json_decode($data);
var_dump($data);
