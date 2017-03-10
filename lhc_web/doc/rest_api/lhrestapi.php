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
	private function executeRequest($function, $params, $uparams = array(), $method = 'GET', $manualAppend = '')
	{
		$ch = curl_init();
		$headers = array('Accept' => 'application/json');

		$uparamsArg = '';
		
		if (!empty($uparams) && is_array($uparams)) {
		    $parts = array();
		    foreach ($uparams as $param => $value) {
		        $parts[] = '/('.$param .')/'.$value;
		    }
		    $uparamsArg = implode('', $parts);
		    
		}
		
		$requestArgs = ($method == 'GET') ? '?' .http_build_query($params) : '';
		
		if ($method == 'POST') {
		    curl_setopt($ch,CURLOPT_POST,1);
		    curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
		}
		
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username . ':' . $this->apiKey);		
		curl_setopt($ch, CURLOPT_URL, $this->host . '/restapi/' . $function . $manualAppend . $uparamsArg . $requestArgs);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Some hostings produces wargning...
		$content = curl_exec($ch);

		return $content;
	}

	public function execute($function, $params, $uparams = array(), $method = 'GET', $jsonObject = true, $manualAppend = '')
	{
	    $response = $this->executeRequest($function, $params, $uparams, $method, $manualAppend);
	   	    
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