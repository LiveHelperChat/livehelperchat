<?php

$className = base64_decode(htmlspecialchars_decode($Params['user_parameters']['class']));

$currentUser = erLhcoreClassUser::instance();

if (!isset($_SERVER['HTTP_X_CSRFTOKEN']) || !$currentUser->validateCSFRToken($_SERVER['HTTP_X_CSRFTOKEN'])) {
    die('Invalid CSRF Token');
    exit;
}

if ($className == 'LiveHelperChat\Models\mailConv\Delete\DeleteFilter') {
    $schedule = \LiveHelperChat\Models\mailConv\Delete\DeleteFilter::fetch($Params['user_parameters']['schedule']);
    $schedule->removeThis();
} elseif ($className == 'LiveHelperChatExtension\elasticsearch\providers\Delete\DeleteFilter') {
    $schedule = \LiveHelperChatExtension\elasticsearch\providers\Delete\DeleteFilter::fetch($Params['user_parameters']['schedule']);
    $schedule->removeThis();
}

echo "ok";
exit;

?>