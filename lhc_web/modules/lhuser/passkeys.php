<?php

$tpl = erLhcoreClassTemplate::getInstance('lhuser/passkeys.tpl.php');

$options = erLhcoreClassModelChatConfig::fetch('passkey_options');
$data = (array)$options->data;

$errors = array();

if (isset($_POST['StorePasskeyOptions'])) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('user/passkeys');
        exit;
    }

    $definition = array(
        'enabled' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'boolean'
        ),
        'rp_id' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'rp_name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'secured_rp_ids' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        )
    );

    $form = new ezcInputForm(INPUT_POST, $definition);

    if ($form->hasValidData('enabled') && $form->enabled == true) {
        $data['enabled'] = 1;
    } else {
        $data['enabled'] = 0;
    }

    $data['rp_id'] = $form->hasValidData('rp_id') ? trim($form->rp_id) : '';
    $data['rp_name'] = $form->hasValidData('rp_name') ? trim($form->rp_name) : '';
    $data['secured_rp_ids'] = $form->hasValidData('secured_rp_ids') ? trim($form->secured_rp_ids) : '';

    $options->explain = '';
    $options->type = 0;
    $options->hidden = 1;
    $options->identifier = 'passkey_options';
    $options->value = serialize($data);
    $options->saveThis();

    $tpl->set('updated', 'done');
}

if ((string)$Params['user_parameters_unordered']['action'] === 'delete') {

    if (!$currentUser->validateCSFRToken((string)$Params['user_parameters_unordered']['csfr'])) {
        die('Invalid CSFR Token');
    }

    // Passkey list is reloaded by JS, so we only have to say where to go next
    header('Content-type: application/json');

    $passkeysUrl = erLhcoreClassDesign::baseurl('user/passkeys');

    $passkey = erLhcoreClassModelUserPasskey::fetch((int)$Params['user_parameters_unordered']['id']);

    if (!($passkey instanceof erLhcoreClassModelUserPasskey)) {
        echo json_encode(array('location' => $passkeysUrl . '/(error)/not_found'));
        exit;
    }

    $passkeyUser = erLhcoreClassModelUser::fetch($passkey->user_id);

    // Passkey is the only login option left for this user, he would not be able to login anymore
    if ($passkeyUser instanceof erLhcoreClassModelUser && $passkeyUser->passwordless_login == 1) {
        echo json_encode(array('location' => $passkeysUrl . '/(error)/passwordless'));
        exit;
    }

    $passkey->removeThis();

    echo json_encode(array('location' => $passkeysUrl));
    exit;
}

$deleteError = (string)$Params['user_parameters_unordered']['error'];

if ($deleteError === 'passwordless') {
    $errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Passkey was not deleted, because passwordless login is enabled for this user. Disable passwordless login first');
} elseif ($deleteError === 'not_found') {
    $errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Passkey not found');
}

$pages = new lhPaginator();
$pages->items_total = erLhcoreClassModelUserPasskey::getCount();
$pages->translationContext = 'chat/pendingchats';
$pages->serverURL = erLhcoreClassDesign::baseurl('user/passkeys');
$pages->paginate();
$tpl->set('pages', $pages);

$passkeys = $pages->items_total > 0 ? erLhcoreClassModelUserPasskey::getList(array(
    'limit' => $pages->items_per_page,
    'offset' => $pages->low,
    'sort' => 'id DESC'
)) : array();

$userIds = array();

foreach ($passkeys as $passkey) {
    $userIds[(int)$passkey->user_id] = (int)$passkey->user_id;
}

$users = empty($userIds) ? array() : erLhcoreClassModelUser::getList(array(
    'limit' => false,
    'sort' => false,
    'filterin' => array('id' => array_values($userIds))
));

$userMap = array();

foreach ($users as $user) {
    $userMap[(int)$user->id] = $user;
}

$tpl->set('passkeys', $passkeys);
$tpl->set('user_map', $userMap);
$tpl->set('data', $data);
$tpl->set('detected_host', erLhcoreClassUserPasskey::getDetectedHost());
$tpl->set('current_origin', erLhcoreClassSystem::getHost());

if (!empty($errors)) {
    $tpl->set('errors', $errors);
}

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration', 'System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('user/userlist'), 'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration', 'Users')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('user/passkey', 'Passkey options'))
);

?>
