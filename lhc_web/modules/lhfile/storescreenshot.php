<?php

header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

if ($Params['user_parameters_unordered']['hash'] != '' || $Params['user_parameters_unordered']['vid'] != '') {
	
	$checkHash = true;
	
	if ($Params['user_parameters_unordered']['hash'] != '') {
		list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash']);
	} else if ($Params['user_parameters_unordered']['hash_resume'] != '') {		
		list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash_resume']);
	} elseif ($Params['user_parameters_unordered']['vid'] != '') {				
		$vid = erLhcoreClassModelChatOnlineUser::fetchByVid($Params['user_parameters_unordered']['vid']);				
		if ($vid !== false && $vid->chat_id > 0) {
			$chatID = $vid->chat_id;
			$checkHash = false;
		} else {			
			echo json_encode(array('stored' => 'false'));
			exit;
		}
	};
		
	try {
		$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chatID);
		if ( (($checkHash == true && $chat->hash == $hash) || $checkHash == false) && ($chat->status == erLhcoreClassModelChat::STATUS_PENDING_CHAT || $chat->status == erLhcoreClassModelChat::STATUS_ACTIVE_CHAT)) {

			if (isset($_POST['data'])) {
				
			$imgData = base64_decode(str_replace('data:image/png;base64,','',$_POST['data']));
			
			$fileData = erLhcoreClassModelChatConfig::fetch('file_configuration');
			$data = (array)$fileData->data;
									
			if (strlen($imgData) < $data['fs_max']*1024 && $imgData != '') {
			
				$path = 'var/storage/'.date('Y').'y/'.date('m').'/'.date('d').'/'.$chat->id.'/';
				erLhcoreClassFileUpload::mkdirRecursive($path);
				$fileNameHash = sha1($imgData . time());
				file_put_contents($path . $fileNameHash,$imgData);
						
				$imageSize = getimagesize($path . $fileNameHash);
				
				if ($imageSize) {
				
					try {
						$db = ezcDbInstance::get();
						$db->beginTransaction();
						
							if ($chat->screenshot !== false){
								$chat->screenshot->removeThis();
								$chat->screenshot_id = 0;
							}
							
							$fileUpload = new erLhcoreClassModelChatFile();
							$fileUpload->size = strlen($imgData);
							$fileUpload->type = 'image/png';
							$fileUpload->name = $fileNameHash;
							$fileUpload->date = time();
							$fileUpload->user_id = 0;
							$fileUpload->upload_name = 'screenshot.png';
							$fileUpload->file_path = $path;
							$fileUpload->chat_id = $chat->id;
							$fileUpload->extension = 'png';						
							$fileUpload->saveThis();
										
							$chat->screenshot_id = $fileUpload->id;
							$chat->updateThis();
						
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