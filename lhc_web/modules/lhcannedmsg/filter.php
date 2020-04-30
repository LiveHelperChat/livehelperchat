<?php

erLhcoreClassRestAPIHandler::setHeaders();

$chat = erLhcoreClassModelChat::fetch($Params['user_parameters']['chat_id']);

$canned_options = erLhcoreClassModelCannedMsg::groupItems(erLhcoreClassModelCannedMsg::getCannedMessages($chat->dep_id, erLhcoreClassUser::instance()->getUserID()), $chat, erLhcoreClassUser::instance()->getUserData(true));

$cannedMessagesFormated = array()

foreach ($canned_options as $depId => $group) {

    $dataList = explode('_', $depId);

    $typeTitle = '';

    if ($dataList[0] == 0) {
        $typeTitle = htmlspecialchars(erLhcoreClassModelDepartament::fetch($dataList[1]));
    } elseif ($dataList[0] == 1) {
        $typeTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Personal');
    } else {
        $typeTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat', 'Global');
    } ?>

    <optgroup label="<?php echo $typeTitle ?>">
        <?php foreach ($group as $item) : ?>
            <option data-msg="<?php echo htmlspecialchars($item->msg_to_user)?>" data-delay="<?php echo $item->delay?>" value="<?php echo $item->id?>"><?php echo htmlspecialchars($item->message_title)?></option>
        <?php endforeach; ?>
    </optgroup>

}

echo json_encode($canned_options);

//include(erLhcoreClassDesign::designtpl('lhchat/part/canned_messages_options.tpl.php'));

exit;
?>