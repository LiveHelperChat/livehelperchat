<?php

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
$tpl = erLhcoreClassTemplate::getInstance('lhquestionary/getstatus.tpl.php');
$tpl->set('position',$Params['user_parameters_unordered']['position']);

echo $tpl->fetch();
exit;