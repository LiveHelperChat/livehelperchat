<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">
                <span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Notifications about bot chats')?>
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
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

            <form action="<?php echo erLhcoreClassDesign::baseurl('genericbot/notifications')?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">

            <p><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','To receive browser notifications you have to enable them in your account Notifications settings.')?></small></p>

            <label><input type="checkbox" value="on" name="bot_notifications" <?php if ((int)erLhcoreClassModelUserSetting::getSetting('bot_notifications',0)) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Notify me about bot conversation after defined number of user interactions.')?></label>

                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Notify me if visitor writes more than defined number messages.')?></label>
                    <input type="text" class="form-control form-control-sm" name="bot_msg_nm" value="<?php echo (int)erLhcoreClassModelUserSetting::getSetting('bot_msg_nm',3)?>" />
                </div>

                <input type="submit" class="btn btn-secondary btn-sm" name="updateBotSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update')?>">

            </form>



<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>