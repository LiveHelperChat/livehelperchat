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
