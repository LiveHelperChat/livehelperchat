<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">
                <span class="material-icons">info_outline</span>&nbsp;<?php echo htmlspecialchars($user->name_official);?>
            </h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                
            </button>
        </div>
        <div class="modal-body">

            <?php if (isset($updated) && $updated == true) : $hideSuccessButton = true; $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Settings updated'); ?>
                <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
            <?php endif; ?>

            <?php if (isset($error)) : $errors[] = $error; ?>
                <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
            <?php endif; ?>

            <form action="<?php echo erLhcoreClassDesign::baseurl('user/setopstatus')?>/<?php echo $user->id ?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">

                <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

                <div class="form-group">
                    <p><b><?php echo htmlspecialchars($user->name_official);?></b> <?php echo  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','online status')?><br></p>
                    <label><input type="radio" name="onlineStatus" value="0" <?php $user->hide_online == 1 ? print 'checked="checked"' : ''?>> <?php echo  erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Offline')?></label><br>
                    <label><input type="radio" name="onlineStatus" value="1" <?php $user->hide_online == 0 ? print 'checked="checked"' : ''?>> <?php echo  erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Online')?></label>
                </div>

                <?php if (!empty($offline_reasons)) : ?>
                <div class="form-group" id="offline-reasons-container">
                    <label><?php echo  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Reason')?></label>
                    <select name="offlineReason" class="form-control form-control-sm">
                        <option value="0"><?php echo  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','None')?></option>
                        <?php foreach ($offline_reasons as $offlineReason) : ?>
                        <option value="<?php echo $offlineReason['id']?>" <?php $user->offline_reason_id == $offlineReason['id'] ? print 'selected="selected"' : ''?>><?php echo htmlspecialchars($offlineReason['name'] . (!empty($offlineReason['mode']) ? ' [' . erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Online mode') . ']' : ''))?></option>
                        <?php endforeach; ?>
                    </select>
                    <p><small class="text-muted"><?php echo  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Online mode reasons keep the operator online with the selected reason applied.')?></small></p>
                </div>
                <?php endif; ?>

                <input type="submit" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update')?>">

            </form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>