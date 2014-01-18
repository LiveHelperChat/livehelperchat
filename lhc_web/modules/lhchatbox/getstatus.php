<?php

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

$validUnits = array('pixels' => 'px','percents' => '%');

$tpl = erLhcoreClassTemplate::getInstance('lhchatbox/getstatus.tpl.php');
$tpl->set('position',$Params['user_parameters_unordered']['position']);
$tpl->set('top_pos',(!is_null($Params['user_parameters_unordered']['top']) && (int)$Params['user_parameters_unordered']['top'] >= 0) ? (int)$Params['user_parameters_unordered']['top'] : 400);
$tpl->set('units',key_exists((string)$Params['user_parameters_unordered']['units'], $validUnits) ? $validUnits[(string)$Params['user_parameters_unordered']['units']] : 'px');
$tpl->set('widthwidget',(!is_null($Params['user_parameters_unordered']['width']) && (int)$Params['user_parameters_unordered']['width'] > 0) ? (int)$Params['user_parameters_unordered']['width'] : 300);
$tpl->set('heightwidget',(!is_null($Params['user_parameters_unordered']['height']) && (int)$Params['user_parameters_unordered']['height'] > 0) ? (int)$Params['user_parameters_unordered']['height'] : 300);
$tpl->set('heightchatcontent',(!is_null($Params['user_parameters_unordered']['chat_height']) && (int)$Params['user_parameters_unordered']['chat_height'] > 0) ? (int)$Params['user_parameters_unordered']['chat_height'] : 220);
$tpl->set('show_content',(!is_null($Params['user_parameters_unordered']['sc']) && (int)$Params['user_parameters_unordered']['sc'] == 'true') ? true : false);
$tpl->set('show_content_min',(!is_null($Params['user_parameters_unordered']['scm']) && (int)$Params['user_parameters_unordered']['scm']  == 'true') ? true : false);
$tpl->set('disable_min',(!is_null($Params['user_parameters_unordered']['dmn']) && (int)$Params['user_parameters_unordered']['dmn']  == 'true') ? true : false);

echo $tpl->fetch();
exit;