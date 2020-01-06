<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhchat/start.tpl.php');

$tpl->set('department',is_array($Params['user_parameters_unordered']['department']) ? $Params['user_parameters_unordered']['department'] : array());
$tpl->set('id',$Params['user_parameters_unordered']['id'] > 0 ? (int)$Params['user_parameters_unordered']['id'] : null);
$tpl->set('hash',$Params['user_parameters_unordered']['hash'] != '' ? $Params['user_parameters_unordered']['hash'] : null);
$tpl->set('isMobile',$Params['user_parameters_unordered']['mobile'] == 'true');
$tpl->set('theme',$Params['user_parameters_unordered']['theme'] > 0 ? (int)$Params['user_parameters_unordered']['theme'] : null);
$tpl->set('vid',$Params['user_parameters_unordered']['vid'] != '' ? $Params['user_parameters_unordered']['vid'] : null);
$tpl->set('online',erLhcoreClassChat::isOnline(false, false, array(
    'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
    'ignore_user_status' => (isset($_GET['ignore_user_status']) && $_GET['ignore_user_status'] == 'true')
)));

$ts = time();
$tpl->set('captcha',array(
    'hash' => sha1(erLhcoreClassIPDetect::getIP() . $ts . erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' )),
    'ts' => $ts
));

// Prefill by get
if (isset($_GET['prefill']) && is_array($_GET['prefill']) && !empty($_GET['prefill'])) {
    $prefillOptions = array();
    foreach ($_GET['prefill'] as $field => $value) {
        if ($field == 'email') {
            $prefillOptions[] = array('Email' => $value);
        } else if ($field == 'username') {
            $prefillOptions[] = array('Username' => $value);
        }else if ($field == 'phone') {
            $prefillOptions[] = array('Phone' => $value);
        } else if ($field == 'question') {
            $prefillOptions[] = array('Question' => $value);
        }
    }
    $tpl->set('prefill',$prefillOptions);
}

if (isset($_GET['name']) && is_array($_GET['name']) && !empty($_GET['name'])) {
    $attributes = array();
    foreach ($_GET['name'] as $index => $value) {
        $attributes[] = array(
            'show' => (((isset($_GET['sh'][$index]) && ($_GET['sh'][$index] == 'on' || $_GET['sh'][$index] == 'off')) ? $_GET['sh'][$index] : 'b')),
            'value' => $_GET['value'][$index],
            'index' => $index,
            'name' => $value,
            'class' => 'form-control form-control-sm',
            'type' => $_GET['type'][$index],
            'identifier' => ('additional_' . $index),
            'placeholder' => (isset($_GET['placeholder'][$index]) ? $_GET['placeholder'][$index] : ''),
            'width' => (isset($_GET['size'][$index]) ? $_GET['size'][$index] : 6),
            'encrypted' => ($_GET['encattr'][$index] === 't'),
            'required' => ($_GET['req'][$index] === 't'),
            'label' => $value,
        );
    }

    $tpl->set('custom_fields',$attributes);
}

if (isset($_GET['jsvar']) && is_array($_GET['jsvar']) && !empty($_GET['jsvar'])) {
    $tpl->set('jsVars',$_GET['jsvar']);
}

if ($Params['user_parameters_unordered']['theme'] > 0) {
    $Result['theme'] = $Params['user_parameters_unordered']['theme'];
}

if ($Params['user_parameters_unordered']['mobile'] == 'true') {
    $Result['mobile'] = true;
}

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'userchat2';

?>
