<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Name');?></label>
    <input type="text" class="form-control" name="Name"  value="<?php echo htmlspecialchars($item->name);?>" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Nick, what should be operator nick. E.g Support Bot');?></label>
    <input type="text" class="form-control" name="Nick"  value="<?php echo htmlspecialchars($item->nick);?>" />
</div>

<div class="form-group">
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/attr_str_1.tpl.php'));?>
    <input type="text" class="form-control" name="attr_str_1"  value="<?php echo htmlspecialchars($item->attr_str_1);?>" />
</div>

<div class="form-group">
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/attr_str_2.tpl.php'));?>
    <input type="text" class="form-control" name="attr_str_2"  value="<?php echo htmlspecialchars($item->attr_str_2);?>" />
</div>

<div class="form-group">
    <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/attr/attr_str_3.tpl.php'));?>
    <input type="text" class="form-control" name="attr_str_3"  value="<?php echo htmlspecialchars($item->attr_str_3);?>" />
</div>