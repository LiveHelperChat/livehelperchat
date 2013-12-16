<?php

$def = include 'pos/lhchat/erlhcoreclassmodelchat.php';
$def->table =  erLhcoreClassModelChatArchiveRange::$archiveTable;
$def->class = 'erLhcoreClassModelChatArchive';

return $def;

?>