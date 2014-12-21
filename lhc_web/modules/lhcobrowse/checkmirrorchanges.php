<?php 

if (is_numeric($Params['user_parameters']['chat_id']))
{
    $chat = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
    if ( erLhcoreClassChat::hasAccessToRead($chat) )
    {
         $browse = erLhcoreClassCoBrowse::getBrowseInstance($chat);
         if ($browse->modifications != '') {
         	$changes = json_decode($browse->modifications);
         	$changes[] = array('url' => $browse->url);
         	$changes[] = array('base' => $browse->url);
         	$changes[] = array('lmsg' => $browse->mtime > 0 ? $browse->mtime_front : '');
         	$changes[] = array('finished' => array('status' => !$browse->is_sharing,'text' => $browse->is_sharing == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Screen sharing session has finished') : erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Screen is shared')));
         	if ($browse->finished == 1) {
         		$changes[] = array('clear' => true);
         	}
         	echo json_encode($changes);         	
         	$browse->modifications = '';
         	$browse->saveThis();
         } else {
         	$changes = array();
         	$changes[] = array('lmsg' => $browse->mtime > 0 ? $browse->mtime_front : '');
         	$changes[] = array('finished' => array('status' => !$browse->is_sharing,'text' => $browse->is_sharing == 0 ? erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Screen sharing session has finished') : erTranslationClassLhTranslation::getInstance()->getTranslation('cobrowse/browse','Screen is shared')));
         	$changes[] = array('base' => $browse->url);
         	if ($browse->finished == 1) {
         		$changes[] = array('clear' => true); 
         	}      	
         	echo json_encode($changes); 
         }
    }
}

exit;
?>