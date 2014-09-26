<?php


$tpl = erLhcoreClassTemplate::getInstance( 'lhpermission/modulefunctions.tpl.php');
$tpl->set('functions',erLhcoreClassModules::getModuleFunctions($Params['user_parameters']['module_path']));

echo json_encode(array('error' => 'false', 'result' => $tpl->fetch()));
exit;

?>
