<?php

$tpl = erLhcoreClassTemplate::getInstance('lhwebhooks/pushchat.tpl.php');

$item = new stdClass();
$item->incoming_api_id = 0;
$item->chat_id = '';
$item->chat_id_2 = '';
$item->message = '';
$item->dep_id = 0;
$item->create_chat = false;
$item->close_chat = false;

// Pre-select webhook from GET param
$webhookId = filter_input(INPUT_GET, 'webhook_id', FILTER_VALIDATE_INT);
if ($webhookId > 0) {
    $item->incoming_api_id = $webhookId;
}

/**
 * Has post data
 */
if (ezcInputForm::hasPostData()) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('webhooks/pushchat');
        exit;
    }

    $definition = [
        'incoming_api_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', ['min_range' => 1]),
        'message' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null),
        'chat_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null),
        'chat_id_2' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw', null),
        'create_chat' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean', null),
        'close_chat' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'boolean', null),
        'dep_id' => new ezcInputFormDefinitionElement(ezcInputFormDefinitionElement::OPTIONAL, 'int', ['min_range' => 1]),
    ];

    $Errors = [];
    $form = new ezcInputForm(INPUT_POST, $definition);

    $webhookItem = null;
    if ($form->hasValidData('incoming_api_id')) {
        $item->incoming_api_id = $form->incoming_api_id;
        try {
            $webhookItem = erLhcoreClassModelChatIncomingWebhook::fetch($item->incoming_api_id);
        } catch (Exception $e) {}
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('twilio/sendmessage','Please choose a webhook!');
    }

    if ($form->hasValidData('chat_id') && !empty($form->chat_id)) {
        $item->chat_id = $form->chat_id;
    } else {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('twilio/sendmessage','Please enter Chat ID 1!');
    }

    if ($form->hasValidData('chat_id_2') && !empty($form->chat_id_2)) {
        $item->chat_id_2 = $form->chat_id_2;
    } elseif ($webhookItem !== null && str_contains($webhookItem->conditions_array['chat_id_template'] ?? '', '{chat_id_2}')) {
        $Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('twilio/sendmessage','Please enter Chat ID 2!');
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

            $chat = erLhcoreClassChatWebhookIncoming::sendMessage(
                erLhcoreClassModelChatIncomingWebhook::fetch($item->incoming_api_id), 
                $item,
                $userData
            );

            $tpl->set('updated', true);
            $tpl->set('chat', $chat);

        } catch (Exception $e) {
            $tpl->set('errors', [$e->getMessage()]);
        }

    } else {
        $tpl->set('errors', $Errors);
    }
}

$tpl->set('item', $item);

// Load webhook configuration for labels/datalists
$webhookConditions = [];
$webhookLoaded = false;
if ($item->incoming_api_id > 0) {
    try {
        $webhookItem = erLhcoreClassModelChatIncomingWebhook::fetch($item->incoming_api_id);
        if ($item->dep_id == 0) {
            $item->dep_id = $webhookItem->dep_id;
        }
        if ($webhookItem instanceof erLhcoreClassModelChatIncomingWebhook) {
            $webhookConditions = $webhookItem->conditions_array;
            $webhookLoaded = true;
        }
    } catch (Exception $e) {
        // webhook not found
    }
}
$tpl->set('webhook_conditions', $webhookConditions);
$tpl->set('webhook_loaded', $webhookLoaded);


$Result['content'] = $tpl->fetch();
$Result['path'] = [
    ['url' => erLhcoreClassDesign::baseurl('system/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module', 'System configuration')],
    ['url' => erLhcoreClassDesign::baseurl('webhooks/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module', 'Webhooks')],
    ['title' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module', 'Push chat')],
];
