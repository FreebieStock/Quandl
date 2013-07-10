Quandl
======

PHP library to use Quandle API - access over 6 million time series data and upload yours

Sample usage:
<pre>

<?php

include __DIR__ . '/lib/Quandl.php';
$token = 'YOUR_API_TOKEN';

$code = 'STATCHINA/P1642';
$quandl = new Quandl($token);
$data = $quandl->download($code);

</pre>
