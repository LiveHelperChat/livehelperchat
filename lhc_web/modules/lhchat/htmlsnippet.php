<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );

$htmlCode = '';

if ($Params['user_parameters']['type'] == 'msg')
{
    if (isset($Params['user_parameters_unordered']['hash']) && !empty($Params['user_parameters_unordered']['hash'])) {
        $canned = erLhcoreClassModelCannedMsg::fetch($Params['user_parameters']['sub_id']);
        $parts = explode('_',$Params['user_parameters_unordered']['hash']);
        $chat = erLhcoreClassModelChat::fetch($parts[0]);
        $message = erLhcoreClassModelmsg::fetch($Params['user_parameters']['id']);
        if ($chat instanceof erLhcoreClassModelChat && $chat->hash == $parts[1] && $chat->id == $message->chat_id) {
            $metaMsgArray = $message->meta_msg_array;
            $metaMsgArray['processed'] = true;
            $message->meta_msg = json_encode($metaMsgArray);
            $message->saveThis();
            $htmlCode = $canned->html_snippet;
        }
    }
} elseif ($Params['user_parameters']['type'] == 'theme') {
    $theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters']['id']);
    if ($theme instanceof erLhAbstractModelWidgetTheme && $theme->bot_configuration_array['inject_html'] != ''){
        $htmlCode = $theme->bot_configuration_array['inject_html'];
    }
} elseif ($Params['user_parameters']['type'] == 'inv') {
    $invitation = erLhAbstractModelProactiveChatInvitation::fetch($Params['user_parameters']['id']);
    if ($invitation instanceof erLhAbstractModelProactiveChatInvitation && $invitation->design_data_array['inject_html'] != ''){
        $htmlCode = $invitation->design_data_array['inject_html'];
    }
}

if (!empty($htmlCode)) : ?>
(function(){
var setInnerHTML = function(elm, html) {
elm.innerHTML = html;
Array.from(elm.querySelectorAll("script")).forEach( oldScript => {
const newScript = document.createElement("script");
Array.from(oldScript.attributes)
.forEach( attr => newScript.setAttribute(attr.name, attr.value) );
newScript.appendChild(document.createTextNode(oldScript.innerHTML));
oldScript.parentNode.replaceChild(newScript, oldScript);
});
}
var temp = document.createElement('div');
setInnerHTML(temp,<?php echo json_encode(trim($htmlCode), JSON_UNESCAPED_SLASHES)?>);
var parentElement = document.body;
while (temp.firstChild) {
parentElement.appendChild(temp.firstChild, parentElement.childNodes[0]);
};
})();
<?php endif; exit; ?>