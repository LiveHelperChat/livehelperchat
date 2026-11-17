<?php

$reason = isset($_GET['reason']) && is_string($_GET['reason']) ? mb_substr($_GET['reason'], 0, 200) : 'URL';
$tpl = erLhcoreClassTemplate::getInstance('lhmailconv/maskedlink.tpl.php');
$tpl->set('reason', $reason);
echo $tpl->fetch();
exit;