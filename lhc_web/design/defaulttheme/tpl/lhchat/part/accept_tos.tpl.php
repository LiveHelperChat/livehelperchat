<?php if (isset($start_data_fields[$tosVariable]) && $start_data_fields[$tosVariable] == true) : $hasExtraField = true;?>
<div class="form-group">
    <div class="form-check">
        <input id="accept-tos" class="form-check-input <?php if (isset($errors['accept_tos'])) : ?> is-invalid<?php endif;?>" type="checkbox" <?php echo $input_data->accept_tos == true || ((!isset($_POST) || empty($_POST)) && isset($start_data_fields[$tosCheckedVariable]) && $start_data_fields[$tosCheckedVariable] == true) ? 'checked="checked"' : '';?> name="AcceptTOS" value="on">
        <label class="form-check-label form-control-sm" for="accept-tos">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','I accept my personal data will be handled according to');?>&nbsp;<a target="_blank" href="<?php echo erLhcoreClassModelChatConfig::fetch('accept_tos_link')->current_value?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','our terms and to the Law');?></a>
        </label>
    </div>

</div>
<?php endif; ?>