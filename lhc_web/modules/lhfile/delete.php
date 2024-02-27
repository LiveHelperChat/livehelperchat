<?php

if (!$currentUser->validateCSFRToken($Params['user_parameters_unordered']['csfr'])) {
	die('Invalid CSFR Token');
	exit;
}

try {
	$file = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChatFile', $Params['user_parameters']['file_id']);
	$file->removeThis();
} catch (Exception $e) {

}

if (isset($_GET['react'])){
    exit;
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>