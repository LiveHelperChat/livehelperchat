<?php

$templateOverride = 'lhchat/demo.tpl.php';

// Use demo pagelayout
$pagelayoutOverride = 'demo';

// Disable online tracking for demo by session cookies
$_GET['cd'] = 1;

include 'modules/lhchat/start.php';

?>