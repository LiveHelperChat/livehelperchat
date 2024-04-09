<?php $fields = $object->getFields();?>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<div class="form-group">
    <label><?php echo $fields['name']['trans'];?>*</label>
    <?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object)?>
</div>

<div class="form-group">
    <label><?php echo $fields['color']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('color', $fields['color'], $object)?>
</div>

<div class="form-group">
    <label class="mb-0"><?php echo erLhcoreClassAbstract::renderInput('pinned', $fields['pinned'], $object)?> <?php echo $fields['pinned']['trans'];?></label>
    <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Will appear as very first items to select for the subject');?></i></small></p>
</div>

<div class="form-group">
    <label class="mb-0"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/chatsubject','Choose in what widgets this subject will be visible directly');?></label>
    <div class="row">
        <div class="col-6"><label><input type="checkbox" <?php if ($object->widgets & 1) : ?>checked="checked"<?php endif; ?> name="widgets[]" value="1"> Pending chat</label></div>
        <div class="col-6"><label><input type="checkbox" <?php if ($object->widgets & 2) : ?>checked="checked"<?php endif; ?> name="widgets[]" value="2"> Active chat</label></div>
        <div class="col-6"><label><input type="checkbox" <?php if ($object->widgets & 4) : ?>checked="checked"<?php endif; ?> name="widgets[]" value="4"> Bot chats</label></div>
        <div class="col-6"><label><input type="checkbox" <?php if ($object->widgets & 8) : ?>checked="checked"<?php endif; ?> name="widgets[]" value="8"> My active pending chats</label></div>
        <?php /*<div class="col-6"><label><input type="checkbox" <?php if ($object->widgets & 16) : ?>checked="checked"<?php endif; ?> name="widgets[]" value="16"> Active mails</label></div>
        <div class="col-6"><label><input type="checkbox" <?php if ($object->widgets & 32) : ?>checked="checked"<?php endif; ?> name="widgets[]" value="32"> New mails</label></div>*/ ?>
    </div>
</div>
<hr>

<div class="form-group">
    <label class="mb-0"><?php echo erLhcoreClassAbstract::renderInput('internal', $fields['internal'], $object)?>&nbsp;<?php echo $fields['internal']['trans'];?></label>
    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/subject/internal_explain.tpl.php'));?>
</div>

<div class="form-group">
    <label class="mb-0"><?php echo $fields['dep_id']['trans'];?></label>
    <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','If you do not choose any department, subject will be visible for all departments');?></i></small></p>
    <div class="row">
        <?php echo erLhcoreClassAbstract::renderInput('dep_id', $fields['dep_id'], $object)?>
    </div>
</div>

<div class="form-group">
    <label><?php echo $fields['internal_type']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('internal_type', $fields['internal_type'], $object)?>
    <p><small><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','This is usefull if you need additional filtering for your own purposes. Leave it empty in most cases.');?></i></small></p>
</div>

<div class="btn-group" role="group" aria-label="...">
    <input type="submit" class="btn btn-secondary" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
    <input type="submit" class="btn btn-secondary" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
    <input type="submit" class="btn btn-secondary" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
</div>