<?php

include_once dirname(__DIR__) . '/Http.php';

class Quandl_Http_Curl extends Quandl_Http {

	/**
	* CURL resource link
	* 
	* @var resource
	*/
	var $resource;
	
	/**
	* Constructor
	* 
	* @return Quandl_Http_Curl
	*/
	public function __construct() {
		$this->resource = curl_init();
		$this->setOption(CURLOPT_RETURNTRANSFER, TRUE);
	}
	
	/**
	* Cleans CURL connection
	*/
	function __destruct() {
		curl_close($this->resource);
	}
	
	/**
	* Sets option to the CURL resource.
	* See http://www.php.net/manual/en/function.curl-setopt.php for option description
	* 
	* @param int $name Option identifier
	* @param mixed $value Option value
	* @return Quandl_Http_Curl Returns itself for sugar-code
	*/
	public function setOption($name, $value) {
		curl_setopt($this->resource, $name, $value);
		return $this;
	}	
	
	/**
	* Sets multiple CURL options at once
	* 
	* @param array $options Associative array of options
	* @return Quandl_Http_Curl Returns itself for sugar-code
	*/
	public function setOptions($options) {
		curl_setopt_array($this->resource, $options);
		return $this;
	}

	/**
	* Executes CURL request
	*
	* @return String Returns CURL execution result
	*/
	public function execute() {
		return curl_exec($this->resource);
	}
	
	/**
	* Perform a GET request
	* 
	* @param String $url
	* @return String Response
	*/
	public function get($url) {
		$this->setOption(CURLOPT_URL, $url);
		return $this->execute();
	}
	
	/**
	* Perform a POST request
	* 
	* @param String $url
	* @param mixed $data Collection of POST body variables
	* @return String Response
	*/
	public function post($url, $data = array()) {
		// Set POST data
		$this->setOptions(array(
			CURLOPT_URL => $url,
			CURLOPT_POST => count($data),
			CURLOPT_POSTFIELDS => http_build_query($data),
		));

		// Execute request
		$response = $this->execute();
		
		// Bring CURL resource back in GET state
		$this->setOptions(array(
			CURLOPT_POST => 0,
			CURLOPT_POSTFIELDS => '',
		));
		
		// Return results
		return $response;
	}
	
}
