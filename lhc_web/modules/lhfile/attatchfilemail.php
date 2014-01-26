<?php

$currentUser = erLhcoreClassUser::instance();
$currentUser->getUserID();


  $tpl = erLhcoreClassTemplate::getInstance('lhfile/attatchfilemail.tpl.php');
  
  $pages = new lhPaginator();
  $pages->serverURL = erLhcoreClassDesign::baseurl('file/attatchfilemail');
  $pages->items_total = erLhcoreClassChat::getCount(array(),'lh_chat_file');
  $pages->setItemsPerPage(20);
  $pages->paginate();
  
  $items = array();
  if ($pages->items_total > 0) {
  		$items = erLhcoreClassChat::getList(array('offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id DESC'),'erLhcoreClassModelChatFile','lh_chat_file');
  }
  
  $tpl->set('items',$items);
  $tpl->set('pages',$pages);
  
  $Result['content'] = $tpl->fetch();
  $Result['pagelayout'] = 'popup';
  
?>