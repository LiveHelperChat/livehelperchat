<?php

class erLhcoreClassXMP {

	public static function sendTestXMP($userData) {		
		$data = (array) erLhcoreClassModelChatConfig::fetch('xmp_data')->data;				
		$conn = new XMPPHP_XMPP($data['host'], $data['port'], $data['username'], $data['password'], $data['resource'], $data['server'], $printlog = false, $loglevel = XMPPHP_Log::LEVEL_INFO);
		try {
			$conn->connect();
			$conn->processUntil('session_start');
			$conn->presence();
			$conn->message($userData->email, $data['xmp_message']);
			$conn->disconnect();
			return true;
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	public static function sendXMPMessage($chat) {
		
		$data = (array) erLhcoreClassModelChatConfig::fetch('xmp_data')->data;
		
		if (isset($data['use_xmp']) && $data['use_xmp'] == 1) {	
				
			$conn = new XMPPHP_XMPP($data['host'], $data['port'], $data['username'], $data['password'], $data['resource'], $data['server'], $printlog = false, $loglevel = XMPPHP_Log::LEVEL_INFO);
			try {
				$conn->connect();
				$conn->processUntil('session_start');
				$conn->presence();
				
				$emailRecipient = array();
				if ($chat->department !== false && $chat->department->email != '') { // Perhaps department has assigned email
					$emailRecipient = explode(',',$chat->department->email);
				} else { // Lets find first user and send him an e-mail
					$list = erLhcoreClassModelUser::getUserList(array('limit' => 1,'sort' => 'id ASC'));
					$user = array_pop($list);
					$emailRecipient = array($user->email);
				}
				
				$messages = array_reverse(erLhcoreClassModelmsg::getList(array('limit' => 5,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id))));
				$messagesContent = '';
				 
				foreach ($messages as $msg ) {
					if ($msg->user_id == -1) {
						$messagesContent .= date('Y-m-d H:i:s',$msg->time).' '. erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','System assistant').': '.htmlspecialchars($msg->msg)."\n";
					} else {
						$messagesContent .= date('Y-m-d H:i:s',$msg->time).' '. ($msg->user_id == 0 ? htmlspecialchars($chat->nick) : htmlspecialchars($msg->name_support)).': '.htmlspecialchars($msg->msg)."\n";
					}
				}
				
				$cfgSite = erConfigClassLhConfig::getInstance();
				$secretHash = $cfgSite->getSetting( 'site', 'secrethash' );
				
				foreach ($emailRecipient as $email) {			
					$veryfyEmail = 	sha1(sha1($email.$secretHash).$secretHash);
					$conn->message($email,str_replace(array('{messages}','{url_accept}'), array($messagesContent,'http://' . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('chat/accept').'/'.erLhcoreClassModelChatAccept::generateAcceptLink($chat).'/'.$veryfyEmail.'/'.$email),$data['xmp_message']));
				}
				
				$conn->disconnect();
				return true;
			} catch (Exception $e) {
				throw $e;
			}
			
		}		
	}

}

?>