<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Internal conversion parameters. Internal conversion is used to track covnersion where final step is done by user executing specific action and letting website to know us it happened.');?></p>

<div class="form-group">
    <label><?php echo $fields['conversion_expires_in']['trans'];?></label>
    <select class="form-control form-control-sm" name="AbstractInput_conversion_expires_in">
        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Choose duration');?></option>
        <option value="60" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 60) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minute');?></option>
        <option value="300" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 300) : ?>selected="selected"<?php endif;?> >5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="600" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 600) : ?>selected="selected"<?php endif;?> >10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="1800" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 1800) : ?>selected="selected"<?php endif;?> >30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="3600" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 3600) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hour');?></option>
        <option value="7200" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 7200) : ?>selected="selected"<?php endif;?> >2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="14400" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 14400) : ?>selected="selected"<?php endif;?> >4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="28800" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 28800) : ?>selected="selected"<?php endif;?> >8 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="57600" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 57600) : ?>selected="selected"<?php endif;?> >16 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="86400" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 86400) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','day');?></option>
        <option value="<?php echo 86400 * 2?>" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 86400 * 2) : ?>selected="selected"<?php endif;?> >2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','days');?></option>
        <option value="<?php echo 86400 * 3?>" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 86400 * 3) : ?>selected="selected"<?php endif;?> >3 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','days');?></option>
        <option value="<?php echo 86400 * 4?>" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 86400 * 4) : ?>selected="selected"<?php endif;?> >4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','days');?></option>
        <option value="<?php echo 86400 * 5?>" <?php if (isset($object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in']) && $object->{$fields['conversion_expires_in']['main_attr']}['conversion_expires_in'] == 86400 * 5) : ?>selected="selected"<?php endif;?> >5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','days');?></option>
    </select>
</div>

<div class="form-group">
    <label><?php echo $fields['event_id']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('event_id', $fields['event_id'], $object)?>
</div>

<div class="form-group">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation', 'Sample javascript to finish conversion');?></label>
<textarea readonly="readonly" rows="3" class="form-control form-control-sm"><?php echo htmlspecialchars("
<script>
window.\$_LHC.eventListener.emitEvent('conversion',['ordered']);
</script>");?></textarea>
</div>


