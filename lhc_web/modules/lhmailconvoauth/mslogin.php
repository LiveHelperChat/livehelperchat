<?php

$mailbox = erLhcoreClassModelMailconvMailbox::fetch($Params['user_parameters']['id']);

$Auth = new LiveHelperChat\mailConv\OAuth\OAuthMSRequest();
$Auth->loginAction($mailbox);

exit;

?>