<?php

$hash = (string)$Params['user_parameters']['hash'];
$format = (string)$Params['user_parameters_unordered']['format'] == 'xml' ? 'xml' : 'json';

$hashSecret = erLhcoreClassModelChatConfig::fetch('export_hash')->current_value;

if (sha1('getcount'.$hashSecret) == $hash) {


}


exit;

?>