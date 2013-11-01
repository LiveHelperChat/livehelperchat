<select name="ModuleFunction">
<option value="*"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/modulefunctions','All functions');?></option>
<?php foreach ($functions as $key => $Function) : ?>
    <option value="<?php echo $key?>"><?php echo htmlspecialchars($Function['explain'])?></option>
<?php endforeach; ?>
</select>