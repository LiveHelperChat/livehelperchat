<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">
                <span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Notifications about mails')?>
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

            <form action="<?php echo erLhcoreClassDesign::baseurl('mailconv/notifications')?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">

                <p><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','To receive browser notifications you have to enable them in your account Notifications settings.')?></small></p>

                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','When an email takes X mail queue time.')?></label>
                    <?php $valuePending = (int)erLhcoreClassModelUserSetting::getSetting('malarm_p', -1)?>
                    <select class="form-control form-control-sm" name="malarm_p">
                        <option value="-1" <?php echo $valuePending == -1 ? 'selected="selected"' : ''?>>Do not inform</option>
                        <option value="30" <?php echo $valuePending == 30 ? 'selected="selected"' : ''?>>30 seconds</option>
                        <option value="60" <?php echo $valuePending == 60 ? 'selected="selected"' : ''?>>1 minute</option>
                        <option value="120" <?php echo $valuePending == 120 ? 'selected="selected"' : ''?>>2 minutes</option>
                        <option value="180" <?php echo $valuePending == 180 ? 'selected="selected"' : ''?>>3 minutes</option>
                        <option value="240" <?php echo $valuePending == 240 ? 'selected="selected"' : ''?>>4 minutes</option>
                        <option value="300" <?php echo $valuePending == 300 ? 'selected="selected"' : ''?>>5 minutes</option>
                        <option value="600" <?php echo $valuePending == 600 ? 'selected="selected"' : ''?>>10 minutes</option>
                        <option value="900" <?php echo $valuePending == 900 ? 'selected="selected"' : ''?>>15 minutes</option>
                        <option value="1800" <?php echo $valuePending == 1800 ? 'selected="selected"' : ''?>>30 minutes</option>
                        <option value="3600" <?php echo $valuePending == 3600 ? 'selected="selected"' : ''?>>1 hour</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','When an email has been accepted by an agent and for X time and has no response.')?></label>
                    <?php $valuePendingResponse = (int)erLhcoreClassModelUserSetting::getSetting('malarm_pr', -1)?>
                    <select class="form-control form-control-sm" name="malarm_pr">
                        <option value="-1" <?php echo $valuePendingResponse == -1 ? 'selected="selected"' : ''?>>Do not inform</option>
                        <option value="30" <?php echo $valuePendingResponse == 30 ? 'selected="selected"' : ''?>>30 seconds</option>
                        <option value="60" <?php echo $valuePendingResponse == 60 ? 'selected="selected"' : ''?>>1 minute</option>
                        <option value="120" <?php echo $valuePendingResponse == 120 ? 'selected="selected"' : ''?>>2 minutes</option>
                        <option value="180" <?php echo $valuePendingResponse == 180 ? 'selected="selected"' : ''?>>3 minutes</option>
                        <option value="240" <?php echo $valuePendingResponse == 240 ? 'selected="selected"' : ''?>>4 minutes</option>
                        <option value="300" <?php echo $valuePendingResponse == 300 ? 'selected="selected"' : ''?>>5 minutes</option>
                        <option value="600" <?php echo $valuePendingResponse == 600 ? 'selected="selected"' : ''?>>10 minutes</option>
                        <option value="900" <?php echo $valuePendingResponse == 900 ? 'selected="selected"' : ''?>>15 minutes</option>
                        <option value="1800" <?php echo $valuePendingResponse == 1800 ? 'selected="selected"' : ''?>>30 minutes</option>
                        <option value="3600" <?php echo $valuePendingResponse == 3600 ? 'selected="selected"' : ''?>>1 hour</option>
                    </select>
                </div>

                <input type="submit" class="btn btn-secondary btn-sm" name="updateMailSettings" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update')?>">

            </form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>