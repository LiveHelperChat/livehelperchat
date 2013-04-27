<?php

header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');
header("Content-type: text/javascript");

$tpl = erLhcoreClassTemplate::getInstance('lhfaq/embed.tpl.php');

echo $tpl->fetch();
exit;