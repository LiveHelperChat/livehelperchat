<?php

/**
 * php cron.php -s site_admin -c cron/util/reparse_url_args
 *
 * Re-parses referrer variable to extract variables from existing chats.
 * */

$pageLimit = 500;
$lastId = 0;

for ($i = 0; $i < 100000; $i++) {

    echo "Saving chat - ",($i + 1),"\n";

    $chats = erLhcoreClassModelChat::getList(array('offset' => 0, 'filtergt' => array('id' => $lastId), 'limit' => $pageLimit, 'sort' => 'id ASC'));
    end($chats);
    $lastMessage = current($chats);

    if (!is_object($lastMessage)) {
        exit;
    }

    $lastId = $lastMessage->id;

    echo $lastId,'-',count($chats),"\n";

    if (empty($chats)){
        exit;
    }

    // Start chat field options
    $startData = erLhcoreClassModelChatConfig::fetch('start_chat_data');
    $startDataFieldsDefault = (array)$startData->data;

    foreach ($chats as $chat) {

        if (($startDataDepartment = erLhcoreClassModelChatStartSettings::findOne(array('filter' => array('department_id' => $chat->dep_id)))) !== false) {
            $start_data_fields = $startDataDepartment->data_array;
        } else {
            $start_data_fields = $startDataFieldsDefault;
        }

        $refererALL = $chat->referrer;
        if ($refererALL != '' && isset($start_data_fields['custom_fields_url']) && $start_data_fields['custom_fields_url'] != '') {
            $queryURL = array();
            preg_match('/(\?|\:\:)(.*?)$/', $refererALL, $queryURL);

            if (isset($queryURL[2])) {
                $referer = $queryURL[2];

                $matchesArray = array();
                preg_match_all('/(.*?)\=(.*?)(\&|\;|$)/', $referer, $matchesArray);

                $argumentsFormatted = array();
                foreach ($matchesArray[1] as $index => $value) {
                    $argumentsFormatted[$value] = $matchesArray[2][$index];
                }

                $stringParts = array();

                if ($referer != '') {
                    $customURLfields = json_decode($start_data_fields['custom_fields_url'], true);
                    if (is_array($customURLfields)) {
                        foreach ($customURLfields as $key => $adminField) {
                            if (isset($argumentsFormatted[$adminField['fieldidentifier']])) {
                                $stringParts[] = array('url' => true, 'identifier' => (isset($adminField['fieldidentifier'])) ? $adminField['fieldidentifier'] : null, 'key' => $adminField['fieldname'], 'value' => $argumentsFormatted[$adminField['fieldidentifier']]);
                            }
                        }
                    }
                }

                if (!empty($stringParts)) {
                    $stringPartsOriginal = json_decode($chat->additional_data,true);

                    $stringPartsOriginalArray = array();
                    if (is_array($stringPartsOriginal)) {
                        foreach ($stringPartsOriginal as $partData) {
                            if (isset($partData['url']) && $partData['url'] == true){
                                $stringPartsOriginalArray[] = $partData['identifier'];
                            }
                        }
                    } else {
                        $stringPartsOriginal = array();
                    }

                    foreach ($stringParts as $part) {
                        if (!in_array($part['identifier'],$stringPartsOriginalArray)){
                            $stringPartsOriginal[] = $part;
                        }
                    }

                    $chat->additional_data = json_encode($stringPartsOriginal);
                    $chat->saveThis();
                    echo "Updating arguments for chat - ",$chat->id,"\n";
                }
            }
        }
    }

}


?>