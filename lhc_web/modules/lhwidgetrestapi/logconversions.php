<?php

erLhcoreClassRestAPIHandler::setHeaders();

$vid = erLhcoreClassModelChatOnlineUser::fetchByVid($Params['user_parameters_unordered']['vid']);

try {
    if ($vid instanceof erLhcoreClassModelChatOnlineUser && (isset($_POST['data']) || isset($_GET['data']))) {
        $data = isset($_POST ['data']) ? $_POST ['data'] : $_GET['data'];
        $jsonData = json_decode($data, true);
        if ($jsonData !== null && is_string($jsonData) && !empty($jsonData)) {
            erLhAbstractModelProactiveChatCampaignConversion::addConversion($vid, $jsonData);
        }
    }
    echo json_encode(array('stored' => 'true'));
    exit;
} catch (Exception $e) {
    echo $e->getMessage();
}
exit ();

exit;
?>