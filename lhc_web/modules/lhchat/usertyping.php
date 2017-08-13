<?php

header('content-type: application/json; charset=utf-8');

try {
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
} catch (Exception $e) {
    $chat = false;
}

if (is_object($chat) && $chat->hash == $Params['user_parameters']['hash'])
{	
		// Rewritten in a more efficient way
		$db = ezcDbInstance::get();
		$stmt = $db->prepare('UPDATE lh_chat SET user_typing = :user_typing, user_typing_txt = :user_typing_txt WHERE id = :id');
		$stmt->bindValue(':id',$chat->id,PDO::PARAM_INT);

        if ( $Params['user_parameters']['status'] == 'true' ) {
        
            $msg = isset($_POST['msg']) ? strip_tags($_POST['msg']) : '';

            if ($msg != '' && strlen($msg) > 200){
            	if ( function_exists('mb_substr') ) {
            		$msg = mb_substr($msg, -200);
            	} else {
            		$msg = substr($msg, -200);
            	}
            }
            $stmt->bindValue(':user_typing',time(),PDO::PARAM_INT);
            $stmt->bindValue(':user_typing_txt',$msg);
        } else {
        	$stmt->bindValue(':user_typing',0,PDO::PARAM_INT);
        	$stmt->bindValue(':user_typing_txt',$chat->user_typing_txt);
        }

        $stmt->execute();
}

echo json_encode(array());
exit;

?>