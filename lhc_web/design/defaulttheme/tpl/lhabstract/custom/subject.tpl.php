<?php $fields = $object->getFields();?>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<div class="form-group">
    <label><?php echo $fields['name']['trans'];?>*</label>
    <?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object)?>
</div>

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