<?php

$response = erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.blockuser', array());

$chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
$currentUser = erLhcoreClassUser::instance();

// We are just in modal window
if (!ezcInputForm::hasPostData()) {
    $tpl = erLhcoreClassTemplate::getInstance('lhchat/blockuser.tpl.php');
    $tpl->set('chat', $chat);
    print $tpl->fetch();
    exit;
}

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
	echo json_encode(array('error' => 'true', 'result' => 'Invalid CSRF Token' ));
	exit;
}

$Errors = array();

if (!($currentUser->hasAccessTo('lhchat','allowblockusers') || $chat->user_id == $currentUser->getUserID())) {
    $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','User blocking failed, perhaps you do not have permission to block users?');
}

$definition = array(
    'btype' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0, 'max_range' => 4),FILTER_REQUIRE_ARRAY
    ),
    'expires' => new ezcInputFormDefinitionElement(
        ezcInputFormDefinitionElement::OPTIONAL, 'int', array( 'min_range' => 0, 'max_range' => 360)
    )
);

$form = new ezcInputForm(INPUT_POST, $definition);
$params = array();

if (!$form->hasValidData('btype') || empty($form->btype)) {
    $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers', 'Please choose a block type!');
} else {
    if (in_array(erLhcoreClassModelChatBlockedUser::BLOCK_IP,$form->btype) && in_array(erLhcoreClassModelChatBlockedUser::BLOCK_NICK,$form->btype)) {
        $params['btype'] = erLhcoreClassModelChatBlockedUser::BLOCK_ALL_IP_NICK;
    } elseif (in_array(erLhcoreClassModelChatBlockedUser::BLOCK_IP,$form->btype) && in_array(erLhcoreClassModelChatBlockedUser::BLOCK_NICK_DEP,$form->btype)) {
        $params['btype'] = erLhcoreClassModelChatBlockedUser::BLOCK_ALL_IP_NICK_DEP;
    } else {
        $btype = $form->btype;
        $params['btype'] = array_shift($btype);
    }
}

if (!$form->hasValidData('expires')) {
    $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers', 'Please choose expire option!');
} else {
    $params['expires'] = time() + ($form->expires * 24 * 3600);
}

$params['chat'] = $chat;

if (empty($Errors)){
    erLhcoreClassModelChatBlockedUser::blockChat($params);
    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/alert_success.tpl.php');
    $tpl->set('msg', erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers', 'Visitor was blocked!'));
    echo json_encode(array('error' => false, 'result' => $tpl->fetch()));
} else {
    $tpl = erLhcoreClassTemplate::getInstance('lhkernel/validation_error.tpl.php');
    $tpl->set('errors', $Errors);
    echo json_encode(array('error' => true, 'result' => $tpl->fetch()));
}

exit;

?>