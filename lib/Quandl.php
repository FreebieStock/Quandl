<?php

class Quandl {
	
	/**
	* .csv format
	*/
	const FORMAT_CSV 	= 'csv';
	/**
	* .html format
	*/
	const FORMAT_HTML 	= 'plain';
	/**
	* .json format
	*/
	const FORMAT_JSON 	= 'json';
	/**
	* .xml format
	*/
	const FORMAT_XML 	= 'xml';
	
	/**
	* Start date in in yyyy-mm-dd format
	*/
	const PARAM_START_DATE = 'trim_start';
	/**
	* End date in in yyyy-mm-dd format
	*/
	const PARAM_END_DATE = 'trim_end';
	/**
	* Parameter specifying time series transformation function
	*/
	const PARAM_TRANSFORM = 'transformation';
	/**
	* Parameter specifying how to collapse time
	*/
	const PARAM_COLLAPSE = 'collapse';
	/**
	* Parameter specifying how to sort data
	*/
	const PARAM_SORT = 'sort_order';
	/**
	* Integer value spcifying how many rows to return
	*/
	const PARAM_ROWS = 'rows';
	/**
	* Boolean value specifying whether to exclude response headers
	*/
	const PARAM_EXCLUDE_HEADERS = 'exclude_headers';
	/**
	* Whether to retrieve only meta data
	*/
	const PARAM_EXCLUDE_DATA = 'exclude_data';
	
	/**
	* Data transformation function
	*/
	const TRANSFORM_NONE = 'none';
	const TRANSFORM_DIFF = 'diff';
	const TRANSFORM_RDIFF = 'rdiff';
	const TRANSFORM_CUMUL = 'cumul';
	const TRANSFORM_NORMALIZE = 'normalize';
	
	/**
	* Period to collapse data
	*/
	const COLLAPSE_NONE = 'none';
	const COLLAPSE_DAILY = 'daily';
	const COLLAPSE_WEEKLY = 'weekly';
	const COLLAPSE_MONTHLY = 'monthly';
	const COLLAPSE_QUARTERLY = 'quarterly';
	const COLLAPSE_ANNUAL = 'annual';
	
	/**
	* Sort data in ascending order
	*/
	const SORT_ASC = 'asc';
	/**
	* Sort data in descending order
	*/
	const SORT_DESC = 'desc';
	
	/**
	* Default format in which to retrieve data
	* 
	* @var String
	*/
	var $defaultFormat = self::FORMAT_JSON;
	
	/**
	* Secure access token
	* 
	* @var string
	*/
	protected $token = '';
	
	/**
	* API access point
	* 
	* @var String
	*/
	var $url = 'http://www.quandl.com/api';
	
	/**
	* Path step in the request URLs also version of the API.
	* 
	* @var String
	*/
	var $version = 'v1';
	
	/**
	* Object that handles GET and POST requests
	* 
	* @var Quandl_Http
	*/
	var $http;
	
	/**
	* Constructor
	* 
	* @param String $token Authentication token that identifies your account. See http://www.quandl.com/users/edit under API tab.
	* @param String Default response format
	* @return Quandl
	*/
	public function __construct($token = '', $format = self::FORMAT_JSON) {
		$this->token = $token;
		$this->defaultFormat = $format;
		
		include_once __DIR__ . '/Quandl/Http/Curl.php';
		$this->http = new Quandl_Http_Curl;
	}
	
	/**
	* Constructs URL string for a request
	* 
	* @param String $resource Resource path/name
	* @param array $params Collection of query parameters, see http://www.quandl.com/api
	* @param String $format Format of data returned
	*/
	public function url($resource, $params = array(), $format = FALSE) {
		// Cast to array just in case object supplied
		$params = (array) $params;
		
		// Use default format if not chosen
		if($format === FALSE) $format = $this->defaultFormat;
		
		// Use auth_token if provided
		if($this->token) $params['auth_token'] = $this->token;
		
		// Build query parameters
		if(count($params)) $params = '?' . http_build_query($params);
		
		return "{$this->url}/{$this->version}/{$resource}.{$format}{$params}";
	}
	
	/**
	* Download timeseries
	* 
	* @param String $code Code of the timeseries
	* @param array $params Collection of query parameters, see http://www.quandl.com/api
	* @param String $format Format of the returned data
	* @return String Response data in requested format
	*/
	public function download($code, $params = array(), $format = FALSE) {
		$url = $this->url('datasets/' . $code, $params, $format);
		return $this->http->get($url);
	}
	
	/**
	* Retrieve favorites list
	* 
	* @return String Response data in requested format
	*/
	public function favorites() {
		$url = $this->url('current_user/collections/datasets/favourites');
		return $this->http->get($url);
	}
	
	/**
	* Request only meta data of the time series
	* 
	* @param String $code Code of the timeseries
	* @param mixed $format Response data format
	* @return String Response data in requested format
	*/
	public function metadata($code, $format = FALSE) {
		return $this->download($code,
			array(self::PARAM_EXCLUDE_DATA => TRUE), $format);
	}
	
	/**
	* Search for data sets on Quandl
	* 
	* @param String $query Search term
	* @param String $format Format of the returned data
	* @return String Response data in requested format
	*/
	public function search($query, $format = FALSE) {
		$url = $this->url('datasets', array('query' => $query), $format);
		return $this->http->get($url);
	}
	
	/**
	* Packs the data from array into a string for submission
	* 
	* @param array $data Time value time series array
	* @return String String of packed data
	*/
	protected function packData($data) {
		$result = array();
		foreach($data as $k => $v) {
			if(is_numeric($k)) $k = self::timestampToDate($k);
			$result[] = $k . ',' . $v;
		}
		return "Date,Value\n" . implode("\n", $result);
	}
	
	/**
	* Uploads time series to Quandl
	* 
	* @param String $code Code of the new time series
	* @param String $title Title of the new time series
	* @param array $data Time series data array consisting of time -> value pairs
	* @param boolean $overwrite Whether to overwrite existing time series
	* @param String $description Description of the time series
	* @return boolean True on success, false otherwise
	*/
	public function upload($code, $title, $data = array(), $overwrite = FALSE,
		$description = '') {
			
		$url = $this->url('datasets', array(), self::FORMAT_JSON);

		$response = $this->http->post($url, array(
			'code' => $code,
			'title' => $title,
			'data' => $this->packData($data),
			'description' => $description,
			'update_or_create' => $overwrite ? 'true' : 'false',
		));
		return (boolean) @json_decode($response);
	}
	
	/**
	* Converts date string into Unix timestamp
	* 
	* @param String $date
	*/
	static public function dateToTimestamp($date) {
		list($year, $month, $date) = explode('-', $date);
		return mktime(0, 0, 0, intval($month), intval($date), intval($year));
	}
	
	/**
	* Converts Unix timestamp to Quandl-formatted date string
	* 
	* @param int $timestamp
	*/
	static public function timestampToDate($timestamp) {
		return date('Y-m-d', $timestamp);
	}
	
}