<?php

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

$tpl = erLhcoreClassTemplate::getInstance('lhdocshare/embed.tpl.php');
$tpl->set('doc_id',(int)$Params['user_parameters']['doc_id']);
$tpl->set('height',(int)$Params['user_parameters_unordered']['height']);

echo $tpl->fetch();
exit;