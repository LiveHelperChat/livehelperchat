<?php 

if (is_numeric($Params['user_parameters']['chat_id']))
{
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
         $browse = erLhcoreClassCoBrowse::getBrowseInstance($chat);
         
         $changes = array();
         $changes[] = array('lmsg' => $browse->mtime > 0 ? $browse->mtime_front : '');
         $changes[] = array('finished' => array('status' => !$browse->is_sharing,'text' => $browse->is_sharing == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Screen sharing session has finished') : erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Screen is shared')));
         if ($browse->finished == 1) {
         	$changes[] = array('clear' => true); 
         }
         $changes[] = array('url' => $browse->url);
         $changes[] = array('base' => $browse->url);
         if ($browse->initialize != '') {
         	$changes[] = json_decode($browse->initialize);
         }

         echo json_encode($changes);          
    }
}

exit;
?>