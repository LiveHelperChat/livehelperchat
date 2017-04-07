<?php 

$tpl = erLhcoreClassTemplate::getInstance('lhcannedmsg/showsuggester.tpl.php');

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
$tpl->set('chat',$chat);

if ( erLhcoreClassChat::hasAccessToRead($chat) ) {
    
    $filter = array();
    
    if (isset($_GET['keyword']) && $_GET['keyword'] != '') {
        $filter['filterlikeright']['tag'] = $_GET['keyword'];
    }
    
    $tpl->set('tags',erLhcoreClassModelCannedMsgTag::getList($filter));
    
    echo json_encode(array('error' => false, 'result' => $tpl->fetch()));
}

exit;

?>