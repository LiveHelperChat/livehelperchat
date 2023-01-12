<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">
                <span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Filter chats by subject')?>
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                
            </button>
        </div>
        <div class="modal-body">

            <?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Settings updated'); ?>
                <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
                <script>
                    setTimeout(function(){
                        location.reload();
                    },250);
                </script>
            <?php endif; ?>

            <form action="<?php echo erLhcoreClassDesign::baseurl('chat/subjectwidget')?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">

                <div class="row" style="max-height: 500px; overflow-y: auto">
                    <?php foreach (erLhAbstractModelSubject::getList(array('sort' => 'name ASC','limit' => false)) as $item) : ?>
                        <div class="col-6">
                            <label><input name="subject_id[]" <?php if (in_array($item->id,$subject_id)) : ?>checked="checked"<?php endif; ?> type="checkbox" value="<?php echo $item->id?>"> <?php echo htmlspecialchars($item->name)?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="fs14 pb-3">
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','On mobile I should receive notification only if chat is one of these states. If you do not choose, you will receive mobile notification in all cases.')?> </div>
                <div>
                    <label class="me-2"><input name="chat_status_id[]" <?php if (in_array(erLhcoreClassModelChat::STATUS_PENDING_CHAT,$status_id)) : ?>checked="checked"<?php endif; ?> type="checkbox" value="<?php echo erLhcoreClassModelChat::STATUS_PENDING_CHAT?>"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Pending chats')?></label>
                    <label class="me-2"><input name="chat_status_id[]" <?php if (in_array(erLhcoreClassModelChat::STATUS_ACTIVE_CHAT,$status_id)) : ?>checked="checked"<?php endif; ?> type="checkbox" value="<?php echo erLhcoreClassModelChat::STATUS_ACTIVE_CHAT?>"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Active chats')?></label>
                    <label class="me-2"><input name="chat_status_id[]" <?php if (in_array(erLhcoreClassModelChat::STATUS_BOT_CHAT,$status_id)) : ?>checked="checked"<?php endif; ?> type="checkbox" value="<?php echo erLhcoreClassModelChat::STATUS_BOT_CHAT?>"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Bot chats')?></label>
                    <label class="me-2"><input name="chat_status_id[]" <?php if (in_array(erLhcoreClassModelChat::STATUS_CLOSED_CHAT,$status_id)) : ?>checked="checked"<?php endif; ?> type="checkbox" value="<?php echo erLhcoreClassModelChat::STATUS_CLOSED_CHAT?>"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Closed chats')?></label>
                    <label class="me-2"><input name="chat_status_id[]" <?php if (in_array(erLhcoreClassModelChat::STATUS_CHATBOX_CHAT,$status_id)) : ?>checked="checked"<?php endif; ?> type="checkbox" value="<?php echo erLhcoreClassModelChat::STATUS_CHATBOX_CHAT?>"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chatbox chats')?></label>
                    <label class="me-2"><input name="chat_status_id[]" <?php if (in_array(erLhcoreClassModelChat::STATUS_OPERATORS_CHAT,$status_id)) : ?>checked="checked"<?php endif; ?> type="checkbox" value="<?php echo erLhcoreClassModelChat::STATUS_OPERATORS_CHAT?>"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Operators chats')?></label>
                </div>

                <input type="hidden" name="Update_action" value="on">

                <input type="submit" class="btn btn-secondary btn-sm" name="Update_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update')?>">
            </form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>