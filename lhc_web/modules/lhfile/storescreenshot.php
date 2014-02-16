<?php

header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

if ($Params['user_parameters_unordered']['hash'] != '' || $Params['user_parameters_unordered']['vid'] != '') {
	
	$checkHash = true;
	$vid = false;
	
	if ($Params['user_parameters_unordered']['hash'] != '') {
		list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash']);
	} else if (1== -1 && $Params['user_parameters_unordered']['hash_resume'] != '') {		
		list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash_resume']);
	} elseif ($Params['user_parameters_unordered']['vid'] != '') {				
		$vid = erLhcoreClassModelChatOnlineUser::fetchByVid($Params['user_parameters_unordered']['vid']);
		
		
		if ($vid !== false) {
			$chatID = $vid->chat_id;
			$checkHash = false;			
		} else {			
			echo json_encode(array('stored' => 'false'));
			exit;
		}
	};
		
	try {
		
		if ($chatID > 0) {
			$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chatID);
		} else {
			$chat = false;
		}
		
		if ( (($checkHash == true && $chat !== false && $chat->hash == $hash) || $checkHash == false) && ( is_object($vid) || ($chat !== false && $chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT))) {
						
			if (isset($_POST['data'])) {
				
			$imgData = base64_decode(str_replace('data:image/png;base64,','',$_POST['data']));
			
			$fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
			$data = (array)$fileData->data;
									
			if (strlen($imgData) < $data['fs_max']*1024 && $imgData != '') {
			
				if ($chat !== false) {
					$path = 'var/storage/'.date('Y').'y/'.date('m').'/'.date('d').'/'.$chat->id.'/';
				} else {
					$path = 'var/storage/'.date('Y').'y/'.date('m').'/'.date('d').'/'.$vid->id.'/';
				}
				
				erLhcoreClassFileUpload::mkdirRecursive($path);
				$fileNameHash = sha1($imgData . time());
				file_put_contents($path . $fileNameHash,$imgData);
						
				$imageSize = getimagesize($path . $fileNameHash);
				
				if ($imageSize) {
				
					
					
					try {
						$db = ezcDbInstance::get();
						$db->beginTransaction();
						
							if ($chat !== false && $chat->screenshot !== false){
								$chat->screenshot->removeThis();
								$chat->screenshot_id = 0;
							}
						
							if (is_object($vid) && $vid->screenshot !== false){
								$vid->screenshot->removeThis();
								$vid->screenshot_id = 0;
							}
							
							$fileUpload = new erLhcoreClassModelChatFile();
							$fileUpload->size = strlen($imgData);
							$fileUpload->type = 'image/png';
							$fileUpload->name = $fileNameHash;
							$fileUpload->date = time();
							$fileUpload->user_id = 0;
							$fileUpload->upload_name = 'screenshot.png';
							$fileUpload->file_path = $path;
							
							if ($chat !== false) {
								$fileUpload->chat_id = $chat->id;
							} else {
								$fileUpload->chat_id = 0;
							}
							
							$fileUpload->extension = 'png';						
							$fileUpload->saveThis();

							if ($chat !== false) {
								$chat->user_typing_txt = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Screenshot ready...');
								$chat->user_typing = time();
								
								$chat->screenshot_id = $fileUpload->id;
								$chat->updateThis();
							}
							
							if ($chat !== false && $chat->online_user !== false) {
								$chat->online_user->screenshot_id = $fileUpload->id;
								$chat->online_user->saveThis();
							} elseif (is_object($vid)) {
								$vid->screenshot_id = $fileUpload->id;
								$vid->saveThis();
							}
							
						$db->commit();
						
						echo json_encode(array('stored' => 'true'));
						exit;
						
					} catch (Exception $e) {								
						$db->rollback();
					}
				}
			}	
		}
		}
	} catch (Exception $e) {
		// Do nothing
	}
}

echo json_encode(array('stored' => 'false'));
exit;
?>