<?php

class erLhcoreClassUpdate
{
	const DB_VERSION = 65;
	const LHC_RELEASE = 186;
	
	public static function getMissingUpdates($data){
		
		$returnLinks = array();
		if ($data['meta']['status'] == 200) {			
			foreach ($data['data'] as $item) {
				if (strpos($item['name'], '.sql') !== false) {
					$version = str_replace(array('.sql','update_'), '', $item['name']);	
					if ($version > self::DB_VERSION) {
						$returnLinks[] = array('url' => $item['html_url'], 'name' => $item['name']);						
					}
				}
			}
		}
		return $returnLinks;
	}	
}

?>