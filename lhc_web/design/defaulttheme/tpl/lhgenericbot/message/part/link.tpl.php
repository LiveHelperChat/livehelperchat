<?php
$linkData = array(
    'start' => '',
    'end' => ''
);

if (isset($itemLink)) {

   if ($itemLink['type'] == 'url') {
        $linkData['start'] = '<a target="_blank" href="' . htmlspecialchars($itemLink['content']['payload']) . '">';
    } elseif ($itemLink['type'] == 'updatechat') {
        $linkData['start'] = '<a data-no-change="true" data-id="'. $messageId .'" data-keep="true" data-payload='. json_encode($itemLink['content']['payload']) .' onclick=\'lhinst.updateChatClicked(' . json_encode($itemLink['content']['payload']) .','. $messageId . ',$(this),true)\'>';
    } elseif ($itemLink['type'] == 'button') {
        $linkData['start'] = '<a data-no-change="true" data-id="'. $messageId .'" data-keep="true" data-payload='. json_encode($itemLink['content']['payload']) .' onclick=\'lhinst.buttonClicked(' . json_encode($itemLink['content']['payload']). ','. $messageId. ',$(this),true)\'>';
    } elseif ($itemLink['type'] == 'trigger') {
        $linkData['start'] = '<a data-no-change="true" data-id="'. $messageId .'" data-keep="true" data-payload='. json_encode($itemLink['content']['payload']) .' onclick=\'lhinst.updateTriggerClicked(' . json_encode($itemLink['content']['payload']). ','.  $messageId. ',$(this),true)\'>';
    }

    if ($linkData['start'] != '') {
        $linkData['end'] = '</a>';
    }
} ?>
