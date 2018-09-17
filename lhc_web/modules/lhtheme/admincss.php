<?php

header("Content-type: text/css; charset: UTF-8");

$adminTheme = erLhAbstractModelAdminTheme::fetch((int)$Params['user_parameters']['id']);

if ($adminTheme instanceof erLhAbstractModelAdminTheme) {
    $tpl = erLhcoreClassTemplate::getInstance('lhtheme/admincss.tpl.php');
    $tpl->set('theme',$adminTheme);
    $tpl->set('cssAttributes', $adminTheme->css_attributes_array);
    echo $tpl->fetch();
}

exit;

?>