<div class="col-4">
    <div class="form-group">
        <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Offline');?>"><input type="checkbox" <?php if (isset($can_edit_groups) && $can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> value="on" name="HideMyStatus" <?php echo $user->hide_online == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Offline')?></label>
    </div>
</div>