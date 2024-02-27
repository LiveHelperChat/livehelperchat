<?php

$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/previewresponsetemplate.tpl.php');

$rtemplate = erLhcoreClassModelMailconvResponseTemplate::fetch($Params['user_parameters']['id']);

if ($rtemplate instanceof erLhcoreClassModelMailconvResponseTemplate)
{
    $tpl->set('response_template',$rtemplate);
} else {
    $tpl->setFile( 'lhchat/errors/adminchatnopermission.tpl.php');
}

echo $tpl->fetch();
exit;

?>