<?php

$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();
$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);

// Chat can be closed only by owner
if ( erLhcoreClassChat::hasAccessToRead($chat) )
{	
  $tpl = erLhcoreClassTemplate::getInstance('lhform/attatchform.tpl.php');
  
  $pages = new lhPaginator();
  $pages->serverURL = erLhcoreClassDesign::baseurl('form/attatchform').'/'.$chat->id;
  $pages->items_total = erLhAbstractModelForm::getCount();
  $pages->setItemsPerPage(20);
  $pages->paginate();
  
  $items = array();
  
  if ($pages->items_total > 0) {
  		$items = erLhAbstractModelForm::getList(array_merge(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id DESC')),'erLhAbstractModelForm','lh_abstract_form');
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