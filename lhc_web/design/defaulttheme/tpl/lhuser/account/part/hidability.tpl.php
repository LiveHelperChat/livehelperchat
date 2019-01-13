<div class="col-4">
    <div class="form-group">
        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Offline');?>"><input type="checkbox" value="on" name="HideMyStatus" <?php echo $user->hide_online == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Offline')?></label>
    </div>
</div>