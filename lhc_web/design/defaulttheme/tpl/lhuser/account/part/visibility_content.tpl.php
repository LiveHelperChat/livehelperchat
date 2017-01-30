<div class="col-xs-6">
   	<div class="form-group">
	  <label title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Chat status will not change upon pending chat opening');?>"><input type="checkbox" value="on" name="UserInvisible" <?php echo $user->invisible_mode == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Invisible mode')?></label>
	</div>
</div>