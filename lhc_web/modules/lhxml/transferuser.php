<?php
$currentUser = erLhcoreClassUser::instance();
if (!$currentUser->isLogged() && !$currentUser->authenticate($_POST['username'],$_POST['password']))
{
    exit;
}

        if (is_numeric( $Params['user_parameters']['chat_id']) && is_numeric($Params['user_parameters']['user_id']))
        {
            $Transfer = new erLhcoreClassModelTransfer();
            $Transfer->chat_id = $Params['user_parameters']['chat_id'];
            $Transfer->user_id = $Params['user_parameters']['user_id'];
            erLhcoreClassTransfer::getSession()->save($Transfer);
        }


exit;
?>