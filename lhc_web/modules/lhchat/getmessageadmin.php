<?php

try {
	$Chat = erLhcoreClassChat::getSession ()->load ( 'erLhcoreClassModelChat', $Params ['user_parameters'] ['chat_id'] );
	
	if (erLhcoreClassChat::hasAccessToRead ( $Chat )) {
					
		$msg = erLhcoreClassModelmsg::fetch ( $Params ['user_parameters'] ['msgid'] );
		
		if ($msg->chat_id == $Chat->id) {
			
			$tpl = erLhcoreClassTemplate::getInstance ( 'lhchat/syncadmin.tpl.php' );
			$tpl->set ( 'messages', array (
					( array ) $msg 
			) );
			$tpl->set ( 'chat', $Chat );
					
			echo json_encode ( array (
					'error' => 'f',
					'msg' => trim ( $tpl->fetch () ) 
			) );
			exit;
		}
	}

} catch ( Exception $e ) {
	
}

echo json_encode ( array (
		'error' => 't'
) );

exit();