<?php

$def = include 'pos/lhchat/erlhcoreclassmodelmsg.php';
$def->table =  erLhcoreClassModelChatArchiveRange::$archiveMsgTable;
$def->class = 'erLhcoreClassModelChatArchiveMsg';

return $def;

?>