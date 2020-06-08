<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">
                <span class="material-icons">info_outline</span>&nbsp;<?php echo htmlspecialchars($user->name_official);?>
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
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

                <div class="form-group">
                    <p><b><?php echo htmlspecialchars($user->name_official);?></b> <?php echo  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','online status')?><br></p>
                    <label><input type="radio" name="onlineStatus" value="0" <?php $user->hide_online == 1 ? print 'checked="checked"' : ''?>> <?php echo  erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Offline')?></label><br>
                    <label><input type="radio" name="onlineStatus" value="1" <?php $user->hide_online == 0 ? print 'checked="checked"' : ''?>> <?php echo  erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Online')?></label>
                </div>

                <input type="submit" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update')?>">

            </form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>