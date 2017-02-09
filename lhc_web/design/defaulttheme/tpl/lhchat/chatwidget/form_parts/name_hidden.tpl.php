<div class="col-xs-6 form-group<?php if (isset($errors['nick'])) : ?> has-error<?php endif;?>">
    <label class="control-label"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?><?php if (isset($start_data_fields['name_require_option']) && $start_data_fields['name_require_option'] == 'required') : ?>*<?php endif;?></label>
    <input type="text" class="form-control" name="Username" value="<?php echo htmlspecialchars($input_data->username);?>" />
</div>