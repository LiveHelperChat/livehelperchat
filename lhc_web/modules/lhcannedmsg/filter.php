<?php

erLhcoreClassRestAPIHandler::setHeaders();

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

$q = (isset($_GET['q']) ? $_GET['q'] : '');

$canned_options = erLhcoreClassModelCannedMsg::groupItems(erLhcoreClassModelCannedMsg::getCannedMessages($chat->dep_id, erLhcoreClassUser::instance()->getUserID(), array(
    'q' => $q
)), $chat, erLhcoreClassUser::instance()->getUserData(true));

$cannedMessagesFormated = array();

$itemSelected = false;
$expandAll = $q != '';
$expandedDefault = false;

foreach ($canned_options as $depId => $group) {

    $dataList = explode('_', $depId);

    $typeTitle = '';

    if ($dataList[0] == 0) {
        $typeTitle = htmlspecialchars(erLhcoreClassModelDepartament::fetch($dataList[1]));
    } elseif ($dataList[0] == 1) {
        $typeTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Personal');
    } else {
        $typeTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Global');
    }

     $items = array();

     foreach ($group as $item) {
         $selected = $itemSelected == false;
         $itemSelected = true;

         $items[] = array(
                 'msg' => $item->msg_to_user,
                 'delay' => $item->delay,
                 'message_title' => $item->message_title,
                 'id' => $item->id,
                 'current' => $selected
         );
     }

     $cannedMessagesFormated[] = array(
        'messages' => $items,
        'title' => $typeTitle,
        'expanded' => ($expandAll || $expandedDefault == false)
     );

    $expandedDefault = true;
}

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('cannedmsg.filter',array('q' => $q, 'cannedmessages' => & $cannedMessagesFormated, 'chat' => & $chat));

echo json_encode($cannedMessagesFormated);

exit;
?>