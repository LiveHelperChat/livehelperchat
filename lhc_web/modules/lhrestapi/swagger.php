<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

header('Content-Type: application/json');

ob_start();
include 'modules/lhrestapi/swagger.json';
$content = ob_get_clean();

$append_definitions = '';
$append_paths = '';
$chats_parameters = '';

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('restapi.swagger', array('append_definitions' => & $append_definitions, 'append_paths' => & $append_paths, 'chats_parameters' => & $chats_parameters));

echo str_replace(array('{{ts}}','{{host}}','{{append_definitions}}','{{append_paths}}', '{{chats_parameters}}'),array(time(),$_SERVER['HTTP_HOST'], $append_definitions, $append_paths, $chats_parameters), $content);

exit;

?>