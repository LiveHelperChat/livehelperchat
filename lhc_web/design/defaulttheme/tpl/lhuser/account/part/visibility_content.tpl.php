<div class="col-4">
   	<div class="form-group">
	  <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Chat status will not change upon pending chat opening');?>"><input <?php if (isset($can_edit_groups) && $can_edit_groups === false) : ?>disabled="disabled"<?php endif;?> type="checkbox" value="on" name="UserInvisible" <?php echo $user->invisible_mode == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Invisible mode')?></label>
	</div>
</div>