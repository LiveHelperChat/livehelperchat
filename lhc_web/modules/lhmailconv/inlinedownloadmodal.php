<?php


$file = erLhcoreClassModelMailconvFile::fetch((int)$Params['user_parameters']['id']);

// Handle if file is archived
if (!($file instanceof \erLhcoreClassModelMailconvFile)) {
    $mailData = \LiveHelperChat\mailConv\Archive\Archive::fetchMailById($Params['user_parameters']['id_conv']);
    if (isset($mailData['mail'])) {
        $file = \LiveHelperChat\Models\mailConv\Archive\File::fetch((int)$Params['user_parameters']['id']);
    }
}

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/inlinedownloadmodal.tpl.php');
$tpl->set('params', $Params['user_parameters']);

echo $tpl->fetch();
exit;

?>