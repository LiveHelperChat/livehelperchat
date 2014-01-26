<?php

$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();
$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

// Chat can be closed only by owner
if ( erLhcoreClassChat::hasAccessToRead($chat) )
{	
  $tpl = erLhcoreClassTemplate::getInstance('lhfile/attatchfile.tpl.php');

  
  $pages = new lhPaginator();
  $pages->serverURL = erLhcoreClassDesign::baseurl('file/attatchfile').'/'.$chat->id;
  $pages->items_total = erLhcoreClassChat::getCount(array(),'lh_chat_file');
  $pages->setItemsPerPage(20);
  $pages->paginate();
  
  $items = array();
  if ($pages->items_total > 0) {
  		$items = erLhcoreClassChat::getList(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id DESC'),'erLhcoreClassModelChatFile','lh_chat_file');
  }
  
  $tpl->set('items',$items);
  $tpl->set('pages',$pages);
   
  

  $tpl->set('chat',$chat);
  $Result['content'] = $tpl->fetch();
  $Result['pagelayout'] = 'popup';

} else {
	exit;
}

?>