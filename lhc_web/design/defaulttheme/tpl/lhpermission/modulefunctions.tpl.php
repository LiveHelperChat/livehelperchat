<select name="ModuleFunction">
<option value="*"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('permission/modulefunctions','All functions');?></option>
<?php foreach ($functions as $key => $Function) : ?>
    <option value="<?=$key?>"><?=$Function['explain']?></option>
<? endforeach; ?>
</select>