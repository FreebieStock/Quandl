Quandl
======

PHP library to use Quandl API - access over 6 million time series data and upload yours.

Quandle site: www.quandl.com

API documentation: www.quandl.com/api

Sample usage:
<pre>

include __DIR__ . '/lib/Quandl.php';

$quandl = new Quandl('YOUR_API_TOKEN');
$data = $quandl->download('STATCHINA/P1642');

</pre>
