<?php
header('content-type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

$vid = erLhcoreClassModelChatOnlineUser::fetchByVid($Params ['user_parameters_unordered'] ['vid']);

try {

    if (is_object($vid) && isset($_POST ['data']) || isset($_GET['data'])) {

        $data = isset($_POST ['data']) ? $_POST ['data'] : $_GET['data'];
        $jsonData = json_decode($data, true);

        if ($jsonData !== null) {
            erLhcoreClassChatEvent::logEvents($jsonData, $vid);
        }

        echo json_encode(array('stored' => 'true'));
        exit;
    }

} catch (Exception $e) {
    echo $e->getMessage();
}
exit ();

?>