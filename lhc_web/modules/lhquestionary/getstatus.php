<?php

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
header("Content-type: text/javascript");

$validUnits = array('pixels' => 'px','percents' => '%');

$tpl = erLhcoreClassTemplate::getInstance('lhquestionary/getstatus.tpl.php');
$tpl->set('position',$Params['user_parameters_unordered']['position']);
$tpl->set('expand',$Params['user_parameters_unordered']['expand']);
$tpl->set('top_pos',(!is_null($Params['user_parameters_unordered']['top']) && (int)$Params['user_parameters_unordered']['top'] >= 0) ? (int)$Params['user_parameters_unordered']['top'] : 400);
$tpl->set('units',key_exists((string)$Params['user_parameters_unordered']['units'], $validUnits) ? $validUnits[(string)$Params['user_parameters_unordered']['units']] : 'px');
$tpl->set('widthwidget',(!is_null($Params['user_parameters_unordered']['width']) && (int)$Params['user_parameters_unordered']['width'] > 0) ? (int)$Params['user_parameters_unordered']['width'] : 300);
$tpl->set('heightwidget',(!is_null($Params['user_parameters_unordered']['height']) && (int)$Params['user_parameters_unordered']['height'] > 0) ? (int)$Params['user_parameters_unordered']['height'] : 300);

echo $tpl->fetch();
exit;