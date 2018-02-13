<?php $fields = $object->getFields();?>

<div class="form-group">
    <label><?php echo $fields['name']['trans'];?>*</label>
    <?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object)?>
</div>

<div class="form-group">
    <label><?php echo $fields['departments']['trans'];?></label>
    <?php echo erLhcoreClassAbstract::renderInput('departments', $fields['departments'], $object)?>
</div>

<div class="btn-group" role="group" aria-label="...">
    <input type="submit" class="btn btn-default" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
    <input type="submit" class="btn btn-default" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
    <input type="submit" class="btn btn-default" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
</div>