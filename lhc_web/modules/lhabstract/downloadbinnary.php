<?php

$ObjectData = erLhcoreClassAbstract::getSession()->load( 'erLhAbstractModel'.$Params['user_parameters']['identifier'], (int)$Params['user_parameters']['object_id'] );

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $ObjectData->file_path_server) . "\n";
finfo_close($finfo);

header('Content-type: '.$mimeType);
header('Content-Disposition: attachment; filename="'.$ObjectData->id.'_file_'.$ObjectData->extension.'"');
echo file_get_contents($ObjectData->file_path_server); 
 
exit; 