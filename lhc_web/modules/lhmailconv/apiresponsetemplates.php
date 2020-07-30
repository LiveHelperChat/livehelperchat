<?php

erLhcoreClassRestAPIHandler::setHeaders();

$message = erLhcoreClassModelMailconvMessage::fetch($Params['user_parameters']['id']);

$conv = $message->conversation;

if ($conv instanceof erLhcoreClassModelMailconvConversation && erLhcoreClassChat::hasAccessToRead($conv) ) {

    $response = [];

    foreach (erLhcoreClassModelMailconvResponseTemplate::getList(['customfilter' => ['dep_id = 0 OR dep_id = ' . $conv->dep_id]]) as $responseTemplate) {
        $response[] = [
            'title' => $responseTemplate->name,
            'content' => str_replace([
                '{operator}',
                '{department}'
            ],[
                $currentUser->getUserData()->name_official,
                $conv->department_name
            ],$responseTemplate->template),
            'description' => ''
        ];
    }
}

echo json_encode($response);
exit;

?>