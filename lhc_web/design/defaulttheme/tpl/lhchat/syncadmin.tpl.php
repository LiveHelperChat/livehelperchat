<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/chat_nick_override_multiinclude.tpl.php'));?>
<?php if ($chat->status != erLhcoreClassModelChat::STATUS_CHATBOX_CHAT) :

    $lastOperatorChanged = false;
    $lastOperatorId = false;
    $lastOperatorNick = '';
    $seeOpName = erLhcoreClassUser::instance()->hasAccessTo('lhchat','see_operator_name');
    $fileData = (array)erLhcoreClassModelChatConfig::fetch('file_configuration')->data;
    $download_policy = 0;

    if (isset($fileData['img_download_policy']) && $fileData['img_download_policy'] == 1) {
        if (erLhcoreClassUser::instance()->hasAccessTo('lhfile','download_unverified')) {
            $download_policy = 0;
        } elseif (erLhcoreClassUser::instance()->hasAccessTo('lhfile','download_verified')) {
            $download_policy = 1;
        } else {
            $download_policy = 2;
        }

    } else {
        $download_policy = 0;
    }


    foreach ($messages as $msg) :
    
        if ($lastOperatorId !== false && ($lastOperatorId != $msg['user_id'] || $lastOperatorNick != $msg['name_support'])) {
            $lastOperatorChanged = true;
        } else {
            $lastOperatorChanged = false;
        }
        
        $lastOperatorId = $msg['user_id'];
        $lastOperatorNick = $msg['name_support'];

        $otherOperator = $msg['user_id'] == -2 || (isset($current_user_id) && $msg['user_id'] > 0 && $msg['user_id'] != $current_user_id);

        if ($msg['meta_msg'] != '') {
            $metaMessageData = json_decode($msg['meta_msg'], true); $messageId = $msg['id'];
        } else if (isset($metaMessageData)) {
            unset($metaMessageData);
        }
        
        // We skip render only if message is empty and it's not one of the supported admin meta messages
        if (
            ($msg['msg'] == '' &&
            (!isset($metaMessageData['content']['text_conditional'])) &&
            (!isset($metaMessageData['content']['chat_operation'])) &&
            (!isset($metaMessageData['content']['extension'])) &&
            (!isset($metaMessageData['content']['survey'])) &&
            (!isset($metaMessageData['content']['warning'])) &&
            (!isset($metaMessageData['content']['html']['content'])) &&
            (!isset($metaMessageData['content']['button_message']))) || (((isset($metaMessageData['content']['attr_options']['as_json']) && $metaMessageData['content']['attr_options']['as_json']) || (isset($metaMessageData['content']['html']['debug']) && $metaMessageData['content']['html']['debug'])) && !erLhcoreClassUser::instance()->hasAccessTo('lhaudit','see_audit_system'))
        ) {
            continue;
        }

        if (isset($metaMessageData['content']['attr_options']['as_json']) && $metaMessageData['content']['attr_options']['as_json']) {
            $metaMessageData['content']['html']['debug'] = true;
            $metaMessageData['content']['html']['content'] = $msg['msg'];
            $msg['msg'] = 'JSON Message';
        }

if ($msg['user_id'] == -1) : ?>
<div class="message-row system-response" id="msg-<?php echo $msg['id']?>" title="<?php echo erLhcoreClassChat::formatDate($msg['time']);?>">
    <span class="usr-tit sys-tit"><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','System assistant')?></i><span class="msg-date ps-2 fw-normal"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></span></span>
        <?php if (isset($paramsMessageRenderExecution['extend_date']) && $paramsMessageRenderExecution['extend_date'] == true) : ?>
            <div class="badge bg-light text-dark"><?php echo erLhcoreClassChat::formatSeconds(time() - $msg['time']);?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','ago at')?> <?php echo erLhcoreClassChat::formatDate($msg['time']);?></div>
        <?php endif; ?>
        <?php if ($msg['msg'] != '') : ?>
            <div class="text-muted"><?php $msgBody = $msg['msg']; $paramsMessageRender = array('print_admin' => (isset($print_admin) && $print_admin === true),'download_policy' => $download_policy, 'operator_render' => true, 'sender' => $msg['user_id'], 'html_as_text' => true); ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?></div>
        <?php endif; ?>

        <?php if (isset($metaMessageData)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render_admin.tpl.php'));?>
        <?php endif; ?>
</div>
<?php else : ?>
<div class="message-row<?php echo $msg['user_id'] == 0 ? ' response' : ' message-admin'.($lastOperatorChanged == true ? ' operator-changes' : '') ?><?php echo $otherOperator == true ? ' other-operator' : ''?><?php if (isset($metaMessageData['content']['whisper'])) : ?> whisper-msg<?php endif;?><?php if (isset($metaMessageData['content']['auto_responder'])) : ?> auto-responder-msg<?php endif;?>" data-op-id="<?php echo $msg['user_id']?>" title="<?php echo erLhcoreClassChat::formatDate($msg['time']);?>" id="msg-<?php echo $msg['id']?>">
    <span class="usr-tit <?php echo $msg['user_id'] == 0 ? ' vis-tit' : ' op-tit'?>">
        <?php $userMessage = false; echo $msg['user_id'] == 0 ? '<i class="material-icons chat-operators mi-fs15 me-0 pb-1">'.($chat->device_type == 0 ? '&#xE30A;' : ($chat->device_type == 1 ? '&#xE32C;' : '&#xE32F;')).'</i> '.htmlspecialchars(isset($lhcNickAlias) ? $lhcNickAlias : $chat->nick) : '<i title="'.$msg['user_id'].'" class="material-icons text-muted chat-operators me-0 mi-fs15">person</i>'.($seeOpName === true && $msg['user_id'] > 0 && ($userMessage = erLhcoreClassModelUser::fetch($msg['user_id'])) !== false ? htmlspecialchars($userMessage->name_official) : '') . (!isset($userMessage) || !is_object($userMessage) || $msg['name_support'] != $userMessage->name_official ? (isset($userMessage) && is_object($userMessage) ? ' <span title="' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','Alias') . '" class="material-icons text-muted mi-fs15 me-0">supervisor_account</span>' : '') . htmlspecialchars($msg['name_support'])  : '')  ?>
    </span>
    <?php if ($msg['user_id'] != 0) : ?><span class="msg-date msg-date-op"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></span><br/><?php endif;?>
    <?php if ($msg['user_id'] == 0) : ?><span class="msg-date msg-date-vi"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></span><br/><?php endif;?>

        <?php $msgBody = $msg['msg']; $paramsMessageRender = array('print_admin' => (isset($print_admin) && $print_admin === true), 'download_policy' => $download_policy, 'operator_render' => true, 'sender' => $msg['user_id'], 'html_as_text' => true, 'see_sensitive_information' => (isset($see_sensitive_information) ? $see_sensitive_information : false));?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>

        <?php if (isset($metaMessageData)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render_admin.tpl.php'));?>
        <?php endif; ?>

    </div>
<?php endif;?>

	<?php endforeach; ?>
<?php else : ?>
	<?php foreach ($messages as $msg ) : ?>
<div class="message-row<?php echo $msg['user_id'] == 0 ? ' response' : ' message-admin'?>" id="msg-<?php echo $msg['id']?>" title="<?php echo erLhcoreClassChat::formatDate($msg['time']);?>">
	<div class="msg-date"><?php echo erLhcoreClassChat::formatDate($msg['time']);?></div>
	<span class="usr-tit<?php echo $msg['user_id'] == 0 ? ' vis-tit' : ' op-tit'?>"><?php echo $msg['user_id'] == 0 ? htmlspecialchars($msg['name_support']) : htmlspecialchars($chat->nick) ?></span>
    <?php $msgBody = $msg['msg']; $paramsMessageRender = array('sender' => $msg['user_id'], 'html_as_text' => true, 'see_sensitive_information' => (isset($see_sensitive_information) ? $see_sensitive_information : false)); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
</div>
<?php endforeach; ?>
<?php endif;?>