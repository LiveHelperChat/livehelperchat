<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

try {
	$form = erLhAbstractModelForm::fetch((int)$Params['user_parameters']['form_id']);
} catch (Exception $e) {
	erLhcoreClassModule::redirect();
	exit;
}

if ($form->active == 0) {
	erLhcoreClassModule::redirect();
	exit;
}

$tpl = erLhcoreClassTemplate::getInstance( 'lhform/fill.tpl.php');
$content_rendered = $form->content_rendered;

$chat = null;
$replaceArray = ['{chat_id}' => '','{hash}' => '', '{msg_id}' => ''];

if (isset($_GET['chat_id']) && is_numeric($_GET['chat_id']) && isset($_GET['hash']) && ($chat = erLhcoreClassModelChat::fetch($_GET['chat_id'])) instanceof erLhcoreClassModelChat && $chat->hash == $_GET['hash'] && $chat->status !== erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
    $tpl->setArray(array(
        'hash' => $_GET['hash'],
        'chat_id' => $_GET['chat_id'],
    ));
    $replaceArray['{chat_id}'] = $chat->id;
    $replaceArray['{hash}'] = $chat->hash;
    if (isset($_GET['msg_id']) && is_numeric($_GET['msg_id'])) {
        $msg = erLhcoreClassModelmsg::fetch($_GET['msg_id']);
        if ($msg instanceof erLhcoreClassModelmsg && $msg->chat_id == $chat->id) {
            $tpl->set('msg_id',$msg->id);
            $replaceArray['{msg_id}'] = $msg->id;
        }
    }
}

if (isset($_POST['chat_id']) && is_numeric($_POST['chat_id']) && isset($_POST['hash']) && ($chat = erLhcoreClassModelChat::fetch($_POST['chat_id'])) instanceof erLhcoreClassModelChat && $chat->hash == $_POST['hash'] && $chat->status !== erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
    $tpl->setArray(array(
        'hash' => $_POST['hash'],
        'chat_id' => $_POST['chat_id'],
    ));
    $replaceArray['{chat_id}'] = $chat->id;
    $replaceArray['{hash}'] = $chat->hash;
    if (isset($_POST['msg_id']) && is_numeric($_POST['msg_id'])) {
        $msg = erLhcoreClassModelmsg::fetch($_POST['msg_id']);
        if ($msg instanceof erLhcoreClassModelmsg && $msg->chat_id == $chat->id) {
            $tpl->set('msg_id',$msg->id);
            $replaceArray['{msg_id}'] = $msg->id;
        }
    }
}

$chatClosed = false;
if (
    (
        (isset($_GET['chat_id']) && is_numeric($_GET['chat_id']) && isset($_GET['hash']) && ($chat = erLhcoreClassModelChat::fetch($_GET['chat_id'])) instanceof erLhcoreClassModelChat && $chat->hash == $_GET['hash']) ||
        (isset($_POST['chat_id']) && is_numeric($_POST['chat_id']) && isset($_POST['hash']) && ($chat = erLhcoreClassModelChat::fetch($_POST['chat_id'])) instanceof erLhcoreClassModelChat && $chat->hash == $_POST['hash'])
    )
    && $chat->status == erLhcoreClassModelChat::STATUS_CLOSED_CHAT
) {
    $chatClosed = true;
    $tpl = erLhcoreClassTemplate::getInstance( 'lhkernel/validation_error.tpl.php');
    $tpl->set('errors',array('chat_closed' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Chat was already closed!')));
}

$tpl->set('replace_array',$replaceArray);
$tpl->set('content',str_replace(array_keys($replaceArray), array_values($replaceArray), $content_rendered));

if ($chatClosed === false && erLhcoreClassFormRenderer::isCollected()) {
	erLhcoreClassFormRenderer::storeCollectedInformation($form, erLhcoreClassFormRenderer::getCollectedInfo(), erLhcoreClassFormRenderer::getCustomFields(), $chat);
};

$tpl->set('form',$form);
$tpl->set('embed_mode',true);
$tpl->set('action_url',erLhcoreClassDesign::baseurl('form/formwidget'));

if (isset($_GET['name']) && is_array($_GET['name']) && !empty($_GET['name'])) {
    $attributes = array();
    foreach ($_GET['name'] as $index => $value) {
        $attributes[] = array(
            'show' => (((isset($_GET['sh'][$index]) && ($_GET['sh'][$index] == 'on' || $_GET['sh'][$index] == 'off')) ? $_GET['sh'][$index] : 'b')),
            'value' => $_GET['value'][$index],
            'index' => $index,
            'name' => $value,
            'class' => 'form-control form-control-sm',
            'type' => isset($_GET['type'][$index]) ? $_GET['type'][$index] : 'hidden',
            'identifier' => ('additional_' . $index),
            'placeholder' => (isset($_GET['placeholder'][$index]) ? $_GET['placeholder'][$index] : ''),
            'width' => (isset($_GET['size'][$index]) ? $_GET['size'][$index] : 6),
            'encrypted' => (isset($_GET['encattr'][$index]) && $_GET['encattr'][$index] === 't'),
            'required' => (isset($_GET['req'][$index]) && $_GET['req'][$index] === 't'),
            'label' => $value,
        );
    }
    $tpl->set('custom_fields',$attributes);
} elseif (isset($_POST['custom_fields'])) {
    $tpl->set('custom_fields',json_decode($_POST['custom_fields']));
}

if (isset($_GET['jsvar']) && is_array($_GET['jsvar']) && !empty($_GET['jsvar'])) {
    $tpl->set('jsVars',$_GET['jsvar']);
} elseif (isset($_POST['jsvar'])) {
    $tpl->set('jsVars',$_POST['jsvar']);
}




$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'widget';
$Result['dynamic_height'] = true;
$Result['dynamic_height_append'] = 10;
$Result['dynamic_height_message'] = 'lhc_sizing_form_embed';
$Result['pagelayout_css_append'] = 'embed-widget embed-fixed';