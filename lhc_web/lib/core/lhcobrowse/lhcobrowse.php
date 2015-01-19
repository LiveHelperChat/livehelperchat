<?php

class erLhcoreClassCoBrowse {
	
	public static function getSession() {
		if (! isset ( self::$persistentSession )) {
			self::$persistentSession = new ezcPersistentSession ( ezcDbInstance::get (), new ezcPersistentCodeManager ( './pos/lhcobrowse' ) );
		}
		return self::$persistentSession;
	}
	
	public static function getBrowseInstance(erLhcoreClassModelChat $chat)
	{
		$coBrowseSession = null;
		$cobrowse = erLhcoreClassModelCoBrowse::getList(array('filter' => array('chat_id' => $chat->id)));
			
		if (empty($cobrowse)) {
			$coBrowseSession = new erLhcoreClassModelCoBrowse();
			$coBrowseSession->chat_id = $chat->id;			
		} else {
			$coBrowseSession = array_pop($cobrowse);
		}
		
		return $coBrowseSession;
	}
	
	public static function addModifications(erLhcoreClassModelChat $chat, $data, $params = array()) {
		$db = ezcDbInstance::get();
		$db->beginTransaction();
		
		try {
			
			$coBrowseSession = self::getBrowseInstance($chat);
			$jsonDatas = json_decode($data);
			
			if (is_array($jsonDatas)) {
				
				if ($coBrowseSession->modifications == '') {
					$changes = array();
				} else {
					$changes = json_decode($coBrowseSession->modifications);
					if (!is_array($changes)) {
						$changes = array();
					}
				}
				
				foreach ($jsonDatas as $jsonData) {
					if ($jsonData->f == 'initialize') {
						$coBrowseSession->initialize = json_encode($jsonData);
						$coBrowseSession->finished = 0;
					} elseif ($jsonData->f == 'cursor'){
						if (isset($jsonData->pos->wh) && isset($jsonData->pos->w) && $jsonData->pos->w > 0 && $jsonData->pos->wh > 0) {
							$coBrowseSession->wh = $jsonData->pos->wh;
							$coBrowseSession->w = $jsonData->pos->w;
						}
						
						if ( isset($jsonData->pos->x) && isset($jsonData->pos->y) ){
							$coBrowseSession->x = $jsonData->pos->x;
							$coBrowseSession->y = $jsonData->pos->y;
						}
					}					
					$changes[] = $jsonData;					
				}
								
				$coBrowseSession->modifications = json_encode($changes);
				
				if (isset($params['base']) && $params['base'] != '') {
					$coBrowseSession->url = $params['base'];
				}
				
				$coBrowseSession->chat_id = $chat->id;				
				$coBrowseSession->saveThis();
			}
			
			$db->commit();
		} catch (Exception $e) {	
			print_r($e);		
   			$db->rollback();
    	}
	}
	
	private static $persistentSession;
}

?>