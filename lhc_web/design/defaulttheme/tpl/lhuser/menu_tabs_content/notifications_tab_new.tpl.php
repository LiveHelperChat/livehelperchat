<div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_notifications') : ?>active<?php endif;?>" id="notifications">
    <div class="form-group">


            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label><input type="checkbox" name="ownntfonly" value="on" <?php $quick_settings['ownntfonly'] == 1 ? print 'checked="checked"' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Show notification only if user is an owner pending chat')?></label>
                        <br/><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','User will stop receive notifications for pending chats if he is not an owner')?></i></small>
                    </div>

                    <div class="form-group">
                        <label><input type="checkbox" name="sn_off" value="on" <?php $quick_settings['sn_off'] == 1 ? print 'checked="checked"' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Show notifications if user is offline')?></label>
                        <br/><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','User will not receive notifications if he is not online')?></i></small>
                    </div>

                    <div class="form-group">
                        <label><input type="checkbox" name="show_alert_chat" value="on" <?php $quick_settings['show_alert_chat'] == 1 ? print 'checked="checked"' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Show alert for new chats')?></label>
                    </div>

                    <div class="form-group">
                        <label><input type="checkbox" name="show_alert_transfer" value="on" <?php $quick_settings['show_alert_transfer'] == 1 ? print 'checked="checked"' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Show alerts for transferred chats')?></label>
                        <br/><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','User will receive alert notification if chat is transferred directly to him. He will be able to accept it directly from alert.')?></i></small>
                    </div>

                    <div class="form-group">
                        <label><input type="checkbox" name="hide_quick_notifications" value="1" <?php $quick_settings['hide_quick_notifications'] == 1 ? print 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Hide quick notifications');?></label>
                        <br/><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Quick notifications are the ones that you see at the top left corner of the application.')?></i></small>
                    </div>

                </div>
                <div class="col-6">

                    <?php if ((int)erLhcoreClassModelChatConfig::fetchCache('activity_track_all')->current_value == 1) : ?>
                        <div class="alert alert-warning"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Activity tracking is set at global level. User settings will be be ignored. Timeout value still will be taken from account settings.')?></div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label><input type="checkbox" name="trackactivity" value="on" <?php $quick_settings['trackactivity'] == 1 ? print 'checked="checked"' : '' ?>> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Change user online/offline status based on his activity')?></label>
                        <br/><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','After certain period of time if no actions are detected user will be marked as offline automatically')?></i></small>
                    </div>

                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Choose timeout value')?></label>
                        <br/><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Select after how long of inactivity user will be marked as offline automatically')?></i></small>
                        <select class="form-control form-control-sm" name="trackactivitytimeout">
                            <option value="-1" <?php echo $quick_settings['trackactivitytimeout'] == -1 ? 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Use default system value')?> (<?php echo (int)erLhcoreClassModelChatConfig::fetchCache('activity_timeout')->current_value?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','minutes')?>)</option>
                            <option value="300" <?php echo $quick_settings['trackactivitytimeout'] == 300 ? 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','minutes')?></option>
                            <option value="600" <?php echo $quick_settings['trackactivitytimeout'] == 600 ? 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','minutes')?></option>
                            <option value="1800" <?php echo $quick_settings['trackactivitytimeout'] == 1800 ? 'selected="selected"' : ''?>>30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','minutes')?></option>
                            <option value="3600" <?php echo $quick_settings['trackactivitytimeout'] == 3600 ? 'selected="selected"' : ''?>>1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','hour')?>
                            <option value="14400" <?php echo $quick_settings['trackactivitytimeout'] == 14400 ? 'selected="selected"' : ''?>>4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','hours')?>
                            <option value="28800" <?php echo $quick_settings['trackactivitytimeout'] == 28800 ? 'selected="selected"' : ''?>>8 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','hours')?>
                            <option value="43200" <?php echo $quick_settings['trackactivitytimeout'] == 43200 ? 'selected="selected"' : ''?>>12 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','hours')?>
                            <option value="86400" <?php echo $quick_settings['trackactivitytimeout'] == 86400 ? 'selected="selected"' : ''?>>1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','day')?>
                            <option value="432000" <?php echo $quick_settings['trackactivitytimeout'] == 432000 ? 'selected="selected"' : ''?>>5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','days')?>
                            <option value="864000" <?php echo $quick_settings['trackactivitytimeout'] == 864000 ? 'selected="selected"' : ''?>>10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','days')?>
                            <option value="1296000" <?php echo $quick_settings['trackactivitytimeout'] == 1296000 ? 'selected="selected"' : ''?>>15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','days')?>
                        </select>
                    </div>

                </div>
            </div>

    </div>
</div>





