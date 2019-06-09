<?php

if (isset($Params['user_parameters_unordered']['hash']) && !empty($Params['user_parameters_unordered']['hash'])) {
    $parts = explode('_',$Params['user_parameters_unordered']['hash']);
    $chat = erLhcoreClassModelChat::fetch($parts[0]);
    $message = erLhcoreClassModelmsg::fetch($Params['user_parameters']['message_id']);
    if ($chat instanceof erLhcoreClassModelChat && $chat->hash == $parts[1] && $chat->id == $message->chat_id) {
        $canned = erLhcoreClassModelCannedMsg::fetch($Params['user_parameters']['canned_id']);
        $metaMsgArray = $message->meta_msg_array;
        $metaMsgArray['processed'] = true;
        $message->meta_msg = json_encode($metaMsgArray);
        $message->saveThis(); ?>
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
            setInnerHTML(temp,<?php echo json_encode(trim($canned->html_snippet), JSON_UNESCAPED_SLASHES)?>);
            var parentElement = document.body;
            while (temp.firstChild) {
                parentElement.appendChild(temp.firstChild, parentElement.childNodes[0]);
            };
            })();
        <?php
    }
}
exit; ?>