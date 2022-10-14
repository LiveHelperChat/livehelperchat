<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Invitation is active if any of these are true.')?></p>

<div class="form-group">
    <label><?php echo $fields['on_op_id']['trans'];?> - <b><?php echo erLhcoreClassUser::instance()->getUserID()?></b></label>
    <?php echo erLhcoreClassAbstract::renderInput('on_op_id', $fields['on_op_id'], $object)?>
</div>

<div class="form-group">
    <label><?php echo $fields['op_max_chats']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('op_max_chats', $fields['op_max_chats'], $object)?>
    <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','If operator has 3 max chats, and you enter here 2. Means proactive invitation will be active only if operator has less than 5 chats assigned to him.')?></i></small></p>
</div>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Other conditions')?></h5>
<div class="form-group">
    <label><?php echo $fields['last_visit_prev']['trans'];?></label>
    <select class="form-control form-control-sm" name="AbstractInput_last_visit_prev">
        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Does not apply');?></option>
        <option value="60" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 60) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minute');?></option>
        <option value="300" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 300) : ?>selected="selected"<?php endif;?> >5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="600" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 600) : ?>selected="selected"<?php endif;?> >10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="1800" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 1800) : ?>selected="selected"<?php endif;?> >30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="3600" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 3600) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hour');?></option>
        <option value="7200" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 7200) : ?>selected="selected"<?php endif;?> >2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="14400" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 14400) : ?>selected="selected"<?php endif;?> >4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="28800" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 28800) : ?>selected="selected"<?php endif;?> >8 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="57600" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 57600) : ?>selected="selected"<?php endif;?> >16 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="86400" <?php if (isset($object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev']) && $object->{$fields['last_visit_prev']['main_attr']}['last_visit_prev'] == 86400) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','day');?></option>
    </select>
    <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','If visitor arrived to website today, and he was on website yesterday. This value holds last time visitor was seen on website yesterday.')?></i></small></p>
</div>

<div class="form-group">
    <label><?php echo $fields['last_chat']['trans'];?></label>
    <select class="form-control form-control-sm" name="AbstractInput_last_chat">
        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Does not apply');?></option>
        <option value="60" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 60) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minute');?></option>
        <option value="300" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 300) : ?>selected="selected"<?php endif;?> >5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="600" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 600) : ?>selected="selected"<?php endif;?> >10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="1800" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 1800) : ?>selected="selected"<?php endif;?> >30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
        <option value="3600" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 3600) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hour');?></option>
        <option value="7200" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 7200) : ?>selected="selected"<?php endif;?> >2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="14400" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 14400) : ?>selected="selected"<?php endif;?> >4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="28800" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 28800) : ?>selected="selected"<?php endif;?> >8 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="57600" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 57600) : ?>selected="selected"<?php endif;?> >16 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
        <option value="86400" <?php if (isset($object->{$fields['last_chat']['main_attr']}['last_chat']) && $object->{$fields['last_chat']['main_attr']}['last_chat'] == 86400) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','day');?></option>
    </select>
</div>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Attributes conditions conditions')?>

    <a href="#" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT+'genericbot/help/proactiveconditions'});" class="material-icons text-muted">help</a>

</h5>
<p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("abstract/widgettheme","You can filter by `online_attr_system` attribute key and it's value. Multiple values can be separated by ||")?></i></small></p>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/events/attributes_conditions.tpl.php'));?>
