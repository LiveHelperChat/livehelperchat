<?php if (isset($metaMessage['reason']) && $metaMessage['reason'] == 'msg_edit' && !(isset($paramsMessageRenderOverride['show_edit_history']) && $paramsMessageRenderOverride['show_edit_history'] === true)) : ?>
    <div class="whisper-msg mb-1">
    <button type="button" class="btn btn-sm btn-link text-decoration-none fs12" title="Click to see a history" onclick="return lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'chat/previewmsg/<?php echo (is_object($msg) ? $msg->id : $msg['id'])?>'})"><span class="material-icons">edit</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Edited')?></button>
    </div>
    <?php else : ?>

<?php
$msgBody = $metaMessage['content'];

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

$paramsMessageRender = array('download_policy' => $download_policy, 'operator_render' => true, 'sender' => (is_object($msg) ? $msg->user_id : $msg['user_id']));
?>
<div class="whisper-msg">
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
    <?php if (isset($metaMessage['content_history'])) : ?>
        <?php foreach ($metaMessage['content_history'] as $msgHistory) : ?>
            <br>
            <?php $msgBody = $msgHistory; $paramsMessageRender = array('download_policy' => $download_policy, 'operator_render' => true, 'sender' => $msg['user_id']);?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php endif; ?>