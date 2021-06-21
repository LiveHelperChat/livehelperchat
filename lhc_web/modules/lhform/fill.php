<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhform/fill.tpl.php');

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

$tpl->set('content',$form->content_rendered);

if (erLhcoreClassFormRenderer::isCollected()) {
	erLhcoreClassFormRenderer::storeCollectedInformation($form, erLhcoreClassFormRenderer::getCollectedInfo(), erLhcoreClassFormRenderer::getCustomFields());
}

$tpl->set('form',$form);

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

if (isset($_GET['chat_id']) && is_numeric($_GET['chat_id']) && isset($_GET['hash']) && ($chat = erLhcoreClassModelChat::fetch($_GET['chat_id'])) instanceof erLhcoreClassModelChat && $chat->hash == $_GET['hash'] && $chat->status !== erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
    $tpl->setArray(array(
        'hash' => $_GET['hash'],
        'chat_id' => $_GET['chat_id'],
    ));
} if (isset($_POST['chat_id']) && is_numeric($_POST['chat_id']) && isset($_POST['hash']) && ($chat = erLhcoreClassModelChat::fetch($_POST['chat_id'])) instanceof erLhcoreClassModelChat && $chat->hash == $_POST['hash'] && $chat->status !== erLhcoreClassModelChat::STATUS_CLOSED_CHAT) {
    $tpl->setArray(array(
        'hash' => $_POST['hash'],
        'chat_id' => $_POST['chat_id'],
    ));
}

$Result['content'] = $tpl->fetch();
$Result['path'] = array(array('title' => (string)$form));
$Result['pagelayout'] = $form->pagelayout != '' ? $form->pagelayout : 'form';
$Result['hide_close_window'] = true;