<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhwebhooks/pushchat.tpl.php');

$item = new stdClass();
$item->incoming_api_id = 0;
$item->chat_id = '';
$item->message = '';
$item->dep_id = 0;
$item->create_chat = false;

/**
 * Has post data
 */
if (ezcInputForm::hasPostData()) {

    $definition = array(
        'incoming_api_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', null),
        'message' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null),
        'chat_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null),
        'create_chat' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean', null),
        'dep_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', null),
    );

    $Errors = array();
    $form = new ezcInputForm(INPUT_POST, $definition);

    // Twilio phone number
    if ($form->hasValidData('chat_id') && !empty($form->chat_id)) {
        $item->chat_id = $form->chat_id;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('twilio/sendmessage','Please enter chatId!');
    }

    // Twilio message
    if ($form->hasValidData('message') && !empty($form->message)) {
        $item->message = $form->message;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('twilio/sendmessage','Please enter a message!');
    }

    // Should we create a chat or just send an SMS
    if ($form->hasValidData('create_chat') && $form->create_chat == true) {
        $item->create_chat = true;
    } else {
        $item->create_chat = false;
    }

    if ($form->hasValidData('incoming_api_id')) {
        $item->incoming_api_id = $form->incoming_api_id;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('twilio/sendmessage','Please choose an API!');
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

            $chat = erLhcoreClassChatWebhookIncoming::sendMessage(erLhcoreClassModelChatIncomingWebhook::fetch($item->incoming_api_id), $item);

            /*$currentUser = erLhcoreClassUser::instance();
            $userData = $currentUser->getUserData();

            $twilio = erLhcoreClassModule::getExtensionInstance('erLhcoreClassExtensionTwilio');
            $chat = $twilio->sendManualMessage(array(
                'msg' => $input->message,
                'phone_number' => $input->phone_number,
                'create_chat' => $input->create_chat,
                'dep_id' => $input->dep_id,
                'operator_id' => $userData->id,
                'name_support' => $userData->name_support,
                'twilio_id' => $input->twilio_id,
            ));*/

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