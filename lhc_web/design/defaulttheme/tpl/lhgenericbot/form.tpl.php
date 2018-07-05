<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" class="form-control" name="Name"  value="<?php echo htmlspecialchars($item->name);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Nick, what should be operator nick. E.g Support Bot');?></label>
    <input type="text" class="form-control" name="Nick"  value="<?php echo htmlspecialchars($item->nick);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Custom attribute 1');?></label>
    <input type="text" class="form-control" name="attr_str_1"  value="<?php echo htmlspecialchars($item->attr_str_1);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Custom attribute 2');?></label>
    <input type="text" class="form-control" name="attr_str_2"  value="<?php echo htmlspecialchars($item->attr_str_2);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Custom attribute 3');?></label>
    <input type="text" class="form-control" name="attr_str_3"  value="<?php echo htmlspecialchars($item->attr_str_3);?>" />
</div>