<?php

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

$tpl = erLhcoreClassTemplate::getInstance('lhform/embed.tpl.php');
$tpl->set('form_id',(int)$Params['user_parameters']['form_id']);
$tpl->set('identifier',isset($_GET['identifier']) && $_GET['identifier'] != '' ? '?identifier='.rawurlencode(rawurldecode($_GET['identifier'])) : '');

echo $tpl->fetch();
exit;