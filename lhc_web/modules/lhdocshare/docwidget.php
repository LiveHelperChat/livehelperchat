<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

try {
	$docShare = erLhcoreClassModelDocShare::fetch((int)$Params['user_parameters']['doc_id']);
} catch (Exception $e) {
	erLhcoreClassModule::redirect();
	exit;
}

if ($docShare->active == 0) {
	erLhcoreClassModule::redirect();
	exit;
}

$tpl = erLhcoreClassTemplate::getInstance('lhdocshare/view.tpl.php');
$tpl->set('docshare',$docShare);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'docsharewidget';
$Result['dynamic_height'] = true;
$Result['dynamic_height_append'] = 10;
$Result['dynamic_height_message'] = 'lhc_sizing_doc_embed_'.$docShare->id;
$Result['pagelayout_css_append'] = 'embed-widget';