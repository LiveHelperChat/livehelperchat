<h1>New chat based on incoming webhook</h1>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('twilio/module','Message was send!'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($chat)) : ?>
    <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('sugarcrm/module', 'Open in a new window'); ?>" ng-click="lhc.startChatNewWindow(<?php echo $chat->id?>,'<?php echo erLhcoreClassDesign::shrt($chat->nick,10,'...',30,ENT_QUOTES);?>')"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('twilio/module','Open chat')?></a>
<?php endif; ?>

<p>You will initiate chat as it was response to incoming webhook.</p>

<form action="" method="post">

<div class="form-group">
    <label>Webhook</label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'incoming_api_id',
        'display_name'   => 'name',
        'css_class'      => 'form-control',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose an API'),
        'selected_id'    => $item->incoming_api_id,
        'list_function'  => 'erLhcoreClassModelChatIncomingWebhook::getList',
        'list_function_params'  => array('filter' => array('disabled' => 0))
    ) ); ?>
</div>

<div class="form-group">
    <label>Recipient. (chatId). In most cases it's just a phone number</label>
    <input type="text" class="form-control" value="<?php echo htmlspecialchars($item->chat_id)?>" name="chat_id">
</div>

<div class="form-group">
    <label>Message</label>
    <textarea class="form-control" name="message"><?php echo htmlspecialchars($item->message)?></textarea>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'dep_id',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
        'selected_id'    => $item->dep_id,
        'css_class'      => 'form-control',
        'list_function'  => 'erLhcoreClassModelDepartament::getList'
    )); ?>
</div>

<div class="form-group">
    <label><input type="checkbox" name="create_chat" value="on" <?php ($item->create_chat == true) ? print 'checked="checked"' : print '';?>>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('sugarcrm/module','Create chat')?></label>
</div>

<input type="submit" name="Update" class="btn btn-primary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('twilio/module','Send')?>">

</form>