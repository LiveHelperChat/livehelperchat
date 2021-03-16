<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhwebhooks/pushchat.tpl.php');

$item = new stdClass();
$item->incoming_api_id = 0;
$item->chat_id = '';
$item->message = '';
$item->dep_id = 0;
$item->create_chat = false;
$item->close_chat = false;

/**
 * Has post data
 */
if (ezcInputForm::hasPostData()) {

    $definition = array(
        'incoming_api_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)),
        'message' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null),
        'chat_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null),
        'create_chat' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean', null),
        'close_chat' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean', null),
        'dep_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 1)),
    );

    $Errors = array();
    $form = new ezcInputForm(INPUT_POST, $definition);

    if ($form->hasValidData('chat_id') && !empty($form->chat_id)) {
        $item->chat_id = $form->chat_id;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('twilio/sendmessage','Please enter chatId!');
    }

    if ($form->hasValidData('message') && !empty($form->message)) {
        $item->message = $form->message;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('twilio/sendmessage','Please enter a message!');
    }

    // Should we create a chat or just send an a message
    if ($form->hasValidData('create_chat') && $form->create_chat == true) {
        $item->create_chat = true;
    } else {
        $item->create_chat = false;
    }

    if ($form->hasValidData('close_chat') && $form->close_chat == true) {
        $item->close_chat = true;
    } else {
        $item->close_chat = false;
    }

    if ($form->hasValidData('incoming_api_id')) {
        $item->incoming_api_id = $form->incoming_api_id;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('twilio/sendmessage','Please choose a webhook!');
    }

    // Set department
    if ($form->hasValidData('dep_id')) {
        $item->dep_id = $form->dep_id;
    } else {
        $item->dep_id = 0;
    }

    if (empty($Errors))
    {
        try {
            $currentUser = erLhcoreClassUser::instance();
            $userData = $currentUser->getUserData();

            $item->user_id = $userData->id;
            $item->name_support = (string)$userData->name_support;

            $chat = erLhcoreClassChatWebhookIncoming::sendMessage(erLhcoreClassModelChatIncomingWebhook::fetch($item->incoming_api_id), $item);

            $tpl->set('updated',true);
            $tpl->set('chat',$chat);

        } catch (Exception $e) {
            $tpl->set('errors',array($e->getMessage()));
        }

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item', $item);

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('webhooks/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Webhooks')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Push chat')),
)

?>