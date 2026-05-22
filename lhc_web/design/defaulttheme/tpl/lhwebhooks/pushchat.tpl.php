<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push', 'New chat based on incoming webhook'); ?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push','Message was send!'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($chat) && $chat->id > 0) : ?>
    <a href="#/chat-id-<?php echo $chat->id?>" class="action-image" data-title="<?php echo erLhcoreClassDesign::shrt($chat->nick,10,'...',30,ENT_QUOTES);?>" onclick="lhinst.startChatNewWindow(<?php echo $chat->id?>,$(this).attr('data-title'))" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push','Open chat')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push','Open chat')?></a>
<?php endif; ?>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push', 'You will initiate chat as it was response to incoming webhook.'); ?></p>

<form action="?webhook_id=<?php echo (int)$item->incoming_api_id?>" method="post" ng-non-bindable>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push', 'Webhook'); ?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'incoming_api_id',
        'display_name'   => 'name',
        'css_class'      => 'form-control form-control-sm',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push','Choose a webhook'),
        'selected_id'    => $item->incoming_api_id,
        'on_change'      => "if(this.value>0){window.location.href='" . erLhcoreClassDesign::baseurl('webhooks/pushchat') . "?webhook_id='+this.value}",
        'list_function'  => 'erLhcoreClassModelChatIncomingWebhook::getList',
        'list_function_params'  => array('limit' => false, 'sort' => '`name` ASC','filter' => array('disabled' => 0))
    ) ); ?>
</div>

<?php if ($webhook_loaded) : ?>

<?php
$chatIdLabel = !empty($webhook_conditions['chat_id_name']) ? htmlspecialchars($webhook_conditions['chat_id_name']) : erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push', 'Chat ID') . ' (chat_id)';
$chatId2Label = !empty($webhook_conditions['chat_id_2_name']) ? htmlspecialchars($webhook_conditions['chat_id_2_name']) : erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push', 'Chat ID 2') . ' (chat_id_2)';
$chatIdList = !empty($webhook_conditions['chat_id_list']) ? array_filter(array_map('trim', explode("\n", $webhook_conditions['chat_id_list']))) : [];
$chatId2List = !empty($webhook_conditions['chat_id_2_list']) ? array_filter(array_map('trim', explode("\n", $webhook_conditions['chat_id_2_list']))) : [];
// If dataset for chat_id_2 is defined and item value is empty, use first dataset value as default
$chatId2Default = !empty($item->chat_id_2) ? htmlspecialchars($item->chat_id_2) : (!empty($chatId2List) ? htmlspecialchars(array_values($chatId2List)[0]) : '');
?>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $chatIdLabel ?></label>
            <input type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($item->chat_id)?>" name="chat_id"<?php echo !empty($chatIdList) ? ' list="datalist_chat_id"' : ''?>>
            <?php if (!empty($chatIdList)) : ?>
            <datalist id="datalist_chat_id">
                <?php foreach ($chatIdList as $chatIdOption) : ?>
                <option value="<?php echo htmlspecialchars($chatIdOption)?>">
                <?php endforeach; ?>
            </datalist>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $chatId2Label ?></label>
            <input type="text" class="form-control form-control-sm" value="<?php echo $chatId2Default?>" name="chat_id_2"<?php echo !empty($chatId2List) ? ' list="datalist_chat_id_2"' : ''?>>
            <?php if (!empty($chatId2List)) : ?>
            <datalist id="datalist_chat_id_2">
                <?php foreach ($chatId2List as $chatId2Option) : ?>
                <option value="<?php echo htmlspecialchars($chatId2Option)?>">
                <?php endforeach; ?>
            </datalist>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push', 'Message'); ?></label>
    <textarea class="form-control form-control-sm" name="message"><?php echo htmlspecialchars($item->message)?></textarea>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push','Department. If you do not choose one we will use the one defined in webhook.');?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'dep_id',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
        'selected_id'    => $item->dep_id,
        'css_class'      => 'form-control form-control-sm',
        'list_function_params'  => array('limit' => false, 'sort' => '`name` ASC'),
        'list_function'  => 'erLhcoreClassModelDepartament::getList'
    )); ?>
</div>

<div class="form-group">
    <label><input type="checkbox" name="create_chat" value="on" <?php ($item->create_chat == true) ? print 'checked="checked"' : print '';?>>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push','Create chat')?></label>
</div>

<div class="form-group">
    <label><input type="checkbox" name="close_chat" value="on" <?php ($item->close_chat == true) ? print 'checked="checked"' : print '';?>>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push','Set created chat status as closed. Visitor reply will initiate chat according to incoming webhook configuration.')?></label>
</div>

<input type="submit" name="Update" class="btn btn-primary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push','Send')?>">

<?php endif; ?>

</form>