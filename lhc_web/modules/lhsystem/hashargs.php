<?php
header ( 'content-type: application/json; charset=utf-8' );

$cfg = erConfigClassLhConfig::getInstance();

// We just generate hash which we later verify just.
$hash = md5($_POST['args'] . $cfg->getSetting( 'site', 'secrethash' ));

echo json_encode("/(h)/" . $hash . $_POST['args']);
exit;

?>