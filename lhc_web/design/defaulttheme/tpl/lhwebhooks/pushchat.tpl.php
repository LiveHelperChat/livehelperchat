<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push', 'New chat based on incoming webhook'); ?></h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push','Message was send!'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($chat)) : ?>
    <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push', 'Open in a new window'); ?>" ng-click="lhc.startChatNewWindow(<?php echo $chat->id?>,'<?php echo erLhcoreClassDesign::shrt($chat->nick,10,'...',30,ENT_QUOTES);?>')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push','Open chat')?></a>
<?php endif; ?>

<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push', 'You will initiate chat as it was response to incoming webhook.'); ?></p>

<form action="" method="post" ng-non-bindable>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push', 'Webhook'); ?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'incoming_api_id',
        'display_name'   => 'name',
        'css_class'      => 'form-control',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push','Choose a webhook'),
        'selected_id'    => $item->incoming_api_id,
        'list_function'  => 'erLhcoreClassModelChatIncomingWebhook::getList',
        'list_function_params'  => array('filter' => array('disabled' => 0))
    ) ); ?>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push', 'Recipient. (chatId). In most cases it is just a phone number'); ?></label>
    <input type="text" class="form-control" value="<?php echo htmlspecialchars($item->chat_id)?>" name="chat_id">
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push', 'Message'); ?></label>
    <textarea class="form-control" name="message"><?php echo htmlspecialchars($item->message)?></textarea>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhook/push','Department. If you do not choose one we will use the one defined in webhook.');?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'dep_id',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
        'selected_id'    => $item->dep_id,
        'css_class'      => 'form-control',
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

</form>