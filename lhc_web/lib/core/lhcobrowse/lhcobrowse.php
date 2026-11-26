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
	
	public static function getBrowseInstanceByOnlineUser(erLhcoreClassModelChatOnlineUser $ouser)
	{
		$coBrowseSession = null;
		$cobrowse = erLhcoreClassModelCoBrowse::getList(array('filter' => array('online_user_id' => $ouser->id)));
			
		if (empty($cobrowse)) {
			$coBrowseSession = new erLhcoreClassModelCoBrowse();
			$coBrowseSession->online_user_id = $ouser->id;			
		} else {
			$coBrowseSession = array_pop($cobrowse);
		}
		
		return $coBrowseSession;
	}
	
	
	
	public static function addModifications($object, $data, $params = array()) {
		$db = ezcDbInstance::get();
		$db->beginTransaction();
		
		try {
			
		    if ($object instanceof erLhcoreClassModelChat){
			     $coBrowseSession = self::getBrowseInstance($object);
		    } elseif ($object instanceof erLhcoreClassModelChatOnlineUser) {
		        $coBrowseSession = self::getBrowseInstanceByOnlineUser($object);
		    };
		    
			$jsonDatas = json_decode($data);
			
			if (is_array($jsonDatas)) {
				$disableJs = true;
				if (class_exists('erLhcoreClassModelChatConfig')) {
					$config = erLhcoreClassModelChatConfig::fetch('disable_js_execution');
					if (is_object($config) && isset($config->current_value)) {
						$disableJs = ((int)$config->current_value === 1);
					}
				}
				self::sanitizeData($jsonDatas, $disableJs);
				
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
				
				/**
				 * Set attributes depending on provided object type
				 * */
				if ($object instanceof erLhcoreClassModelChat){
				    $coBrowseSession->chat_id = $object->id;	
				    
				    if ($object->online_user_id > 0){
				        $coBrowseSession->online_user_id = $object->online_user_id;
				    }
				} elseif ($object instanceof erLhcoreClassModelChatOnlineUser) {
				    $coBrowseSession->online_user_id = $object->id;				    
				    if ($object->chat_id > 0){
				        $coBrowseSession->chat_id = $object->chat_id;
				    }
				}
				
				if ($coBrowseSession->id === null) {
				    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('cobrowse.started', array('co_browse' => & $coBrowseSession));
				}
				
				$coBrowseSession->saveThis();
			}
			
			$db->commit();
		} catch (Exception $e) {	
			print_r($e);		
   			$db->rollback();
    	}
	}
	
	/**
	 * Recursively strips dangerous tags, event-handler attributes, and unsafe URL schemes from cobrowse DOM payloads.
	 */
	public static function sanitizeData(&$data, $disableJs = true) {
		if (is_array($data)) {
			foreach ($data as &$item) {
				self::sanitizeData($item, $disableJs);
			}
			unset($item);
		} elseif (is_object($data)) {
			$tagName = (isset($data->tagName) && is_string($data->tagName)) ? strtoupper($data->tagName) : '';

			if ($disableJs && $tagName === 'SCRIPT') {
				$data->tagName = 'NO-SCRIPT';
				if (isset($data->childNodes)) {
					$data->childNodes = array();
				}
				if (isset($data->textContent)) {
					$data->textContent = '';
				}
			} elseif (in_array($tagName, array('OBJECT', 'EMBED', 'APPLET'), true)) {
				$data->tagName = 'NO-EMBED';
				if (isset($data->childNodes)) {
					$data->childNodes = array();
				}
			}

			if (isset($data->attributes) && is_object($data->attributes)) {
				$urlAttrs = array('href', 'src', 'xlink:href', 'action', 'formaction', 'data', 'poster', 'background', 'codebase', 'cite', 'ping');
				foreach (get_object_vars($data->attributes) as $attrKey => $attrVal) {
					$attrLower = strtolower(trim((string)$attrKey));
					if (strpos($attrLower, 'on') === 0 || $attrLower === 'srcdoc' || ($tagName === 'META' && $attrLower === 'http-equiv')) {
						unset($data->attributes->$attrKey);
						continue;
					}
					if (in_array($attrLower, $urlAttrs, true) && is_string($attrVal) && self::isUnsafeUrl($tagName, $attrVal)) {
						unset($data->attributes->$attrKey);
					}
				}
			}

			foreach (get_object_vars($data) as $prop => $val) {
				if ($prop === 'attributes') {
					continue;
				}
				if (is_array($val) || is_object($val)) {
					self::sanitizeData($data->$prop, $disableJs);
				}
			}
		}
	}

	private static function isUnsafeUrl($tagName, $val) {
		$normalized = strtolower(preg_replace('/[\x00-\x20\s]+/', '', $val));
		if (strpos($normalized, 'javascript:') === 0 || strpos($normalized, 'vbscript:') === 0) {
			return true;
		}
		if (strpos($normalized, 'data:') === 0) {
			if ($tagName === 'IMG' || $tagName === 'SOURCE' || $tagName === '') {
				return !preg_match('/^data:image\/(png|jpe?g|gif|webp|bmp|x-icon|avif);/i', $normalized);
			}
			return true;
		}
		return false;
	}

	private static $persistentSession;
}

?>