<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/abstract_form','Updated!'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php $fields = $object->getFields();?>

<div class="form-group">
    <label><?php echo $fields['name']['trans'];?> *</label>
    <?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object)?>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('use_chat_locale', $fields['use_chat_locale'], $object)?> <?php echo $fields['use_chat_locale']['trans'];?></label>
</div>

<?php $translatableItem = array('identifier' => 'subject'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('subject_ac', $fields['subject_ac'], $object)?> <?php echo $fields['subject_ac']['trans'];?></label>
</div>

<?php $translatableItem = array('identifier' => 'from_name'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('from_name_ac', $fields['from_name_ac'], $object)?> <?php echo $fields['from_name_ac']['trans'];?></label>
</div>

<div class="form-group">
    <label><?php echo $fields['from_email']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('from_email', $fields['from_email'], $object)?>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('from_email_ac', $fields['from_email_ac'], $object)?> <?php echo $fields['from_email_ac']['trans'];?></label>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('user_mail_as_sender', $fields['user_mail_as_sender'], $object)?> <?php echo $fields['user_mail_as_sender']['trans'];?></label>
</div>

<div class="form-group">
    <label><?php echo $fields['reply_to']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('reply_to', $fields['reply_to'], $object)?>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('reply_to_ac', $fields['reply_to_ac'], $object)?> <?php echo $fields['reply_to_ac']['trans'];?></label>
</div>

<div class="form-group">
    <label><?php echo $fields['recipient']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('recipient', $fields['recipient'], $object)?>
</div>

<div class="form-group">
    <label><?php echo $fields['bcc_recipients']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('bcc_recipients', $fields['bcc_recipients'], $object)?>
</div>

<?php $translatableItem = array('identifier' => 'content'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

<div class="btn-group" role="group" aria-label="...">
    <input type="submit" class="btn btn-secondary" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
    <input type="submit" class="btn btn-secondary" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
    <input type="submit" class="btn btn-secondary" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
</div>