<?php

class LHCRestAPI {

	private $host = null;
	private $username = null;
	private $apiKey = null;

	/**
	 * 
	 * @param string $host
	 * 
	 * @param string $username
	 * 
	 * @param string $apiKey
	 */
	public function __construct($host, $username, $apiKey) 
	{
		$this->host = $host;
		$this->username = $username;
		$this->apiKey = $apiKey;
	}

	/**
	 * 
	 * @param string $url
	 * 
	 * @return string
	 */
	private function executeRequest($function, $params)
	{
		$ch = curl_init();
		$headers = array('Accept' => 'application/json');

		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->apiKey);		
		curl_setopt($ch, CURLOPT_URL, $this->host . '/restapi/' . $function . '?' .http_build_query($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Some hostings produces wargning...
		$content = curl_exec($ch);

		return $content;
	}
		
	public function execute($function, $params, $jsonObject = true)
	{
	    $response = $this->executeRequest($function, $params);
	    
	    if ($jsonObject == false) {
	        return $response;
	    }
	    
	    $jsonData = json_decode($response);
	    if ($jsonData !== null) {
	        return $jsonData;
	    } else {
	        throw new Exception('Could not parse response - '.$response);
	    }	    
	}
	
}

?>