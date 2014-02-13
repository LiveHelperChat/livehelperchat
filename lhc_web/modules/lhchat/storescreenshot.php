<?php

header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

if ($Params['user_parameters_unordered']['hash'] != '') {
	list($chatID,$hash) = explode('_',$Params['user_parameters_unordered']['hash']);
	try {
		$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $chatID);
		if ($chat->hash == $hash) {

			// Finish tomorrow
			$imgData = base64_decode(str_replace('data:image/png;base64,','',$_POST['data']));
			
			$path = 'var/storage/'.date('Y').'y/'.date('m').'/'.date('d').'/'.$chat->id.'/';
			erLhcoreClassFileUpload::mkdirRecursive($path);
			file_put_contents($path . sha1($imgData),$imgData);
						
			$fileUpload = new erLhcoreClassModelChatFile();
			$fileUpload->size = strlen($imgData);
			$fileUpload->type = 'image/png';
			$fileUpload->name = sha1($imgData);
			$fileUpload->date = time();
			$fileUpload->user_id = 0;
			$fileUpload->upload_name = 'screenshot.png';
			$fileUpload->file_path = $path;
			$fileUpload->chat_id = $chat->id;
			$fileUpload->extension = 'png';						
			$fileUpload->saveThis();
						
			$chat->screenshot_id = $fileUpload->id;
			$chat->updateThis();
			
			echo json_encode(array('stored' => 'true'));		
			exit;	
		}
	} catch (Exception $e) {
		// Do nothing
	}
}

echo json_encode(array('stored' => 'false'));
exit;
?>