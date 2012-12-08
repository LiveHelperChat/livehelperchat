<?php


if (is_numeric( $Params['user_parameters']['chat_id']) && is_numeric($Params['user_parameters']['user_id']))
{
    $Transfer = new erLhcoreClassModelTransfer();
    $Transfer->chat_id = $Params['user_parameters']['chat_id'];
    $Transfer->user_id = $Params['user_parameters']['user_id'];
    erLhcoreClassTransfer::getSession()->save($Transfer);
}

echo json_encode(array('error' => 'false','result' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferuser','Chat was assigned to selected user'),'chat_id' => $Params['user_parameters']['chat_id']));
exit;

?>