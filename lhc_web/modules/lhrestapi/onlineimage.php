<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (is_numeric($Params['user_parameters_unordered']['user_id'])) {
    $online = erLhcoreClassChat::isOnlineUser((int) $Params['user_parameters_unordered']['user_id'], array(
        'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout']
    ));
} elseif (isset($Params['user_parameters_unordered']['department']) && is_array($Params['user_parameters_unordered']['department']) && !empty($Params['user_parameters_unordered']['department'])){

    erLhcoreClassChat::validateFilterIn($Params['user_parameters_unordered']['department']);

    $online = erLhcoreClassChat::isOnline(
        (int)$Params['user_parameters_unordered']['department'],
        true,
        array (
            'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
            'exclude_bot' => (isset($_GET['exclude_bot']) && $_GET['exclude_bot'] == 'true'),
            'ignore_user_status' => (isset($_GET['ignore_user_status']) && $_GET['ignore_user_status'] == 'true')
        )
    );
} else {
    $online = erLhcoreClassChat::isOnline(false, false, array(
        'online_timeout' => (int) erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'],
        'ignore_user_status' => (isset($_GET['ignore_user_status']) && $_GET['ignore_user_status'] == 'true')
    ));
}

if ($online === false && isset($_GET['offline']) && $_GET['offline'] == '0') {
    $image = ImageCreate(1, 1);
    $white = ImageColorAllocate($image, 255, 255, 255);
    ImageFill($image, 0, 0, $white);
    ImageJpeg($image,null, 95);
    header("Content-Type: image/jpeg");
    exit;
}

$width = $_GET['w'] && is_numeric($_GET['w']) && $_GET['w'] < 1000 ? (int)$_GET['w'] : 200;
$height = 40;
$image = ImageCreate($width, $height);
$white = ImageColorAllocate($image, 255, 255, 255);
$black = ImageColorAllocate($image, 0, 0, 0);
$green = ImageColorAllocate($image, 72, 194, 33);

ImageFill($image, 0, 0, $white);
imagettftext($image, 14, 0, 30,25, ($online ? $green : $black), 'design/defaulttheme/fonts/arial.ttf',($online ? (isset($_GET['online']) && strlen($_GET['online']) < 1000 ? $_GET['online'] : 'I\'m online') : (isset($_GET['offline']) && strlen($_GET['offline']) < 1000 ? $_GET['offline'] : 'I\'m offline')));

$font = 'design/defaulttheme/fonts/MaterialIcons-Regularv2.ttf';
imagettftext($image, 20, 0, 2,33, ($online ? $green : $black), $font,$online ? '&#xe3e7;' : '&#xe3e6;');

header("Content-Type: image/jpeg");
ImageJpeg($image,null, 95);
ImageDestroy($image);
exit;

?>