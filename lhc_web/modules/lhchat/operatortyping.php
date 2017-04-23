<?php
header('content-type: application/json; charset=utf-8');

if (is_numeric($Params['user_parameters']['chat_id']))
{
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {    	
    	// Rewritten in a more efficient way
    	$db = ezcDbInstance::get();
    	$stmt = $db->prepare('UPDATE lh_chat SET operator_typing = :operator_typing, operator_typing_id = :operator_typing_id WHERE id = :id');
    	$stmt->bindValue(':id',$chat->id,PDO::PARAM_INT);
    			
        if ( $Params['user_parameters']['status'] == 'true' ) {
        	$stmt->bindValue(':operator_typing',time(),PDO::PARAM_INT);
        	$stmt->bindValue(':operator_typing_id',$currentUser->getUserID(),PDO::PARAM_INT); 
        } else {
        	$stmt->bindValue(':operator_typing',0,PDO::PARAM_INT);
        	$stmt->bindValue(':operator_typing_id',0,PDO::PARAM_INT);  
        }
        
        $stmt->execute();             
    }
}

echo json_encode(array());
exit;
?>