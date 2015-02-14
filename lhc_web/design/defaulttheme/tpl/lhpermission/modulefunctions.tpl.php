<label class="fs12"><input class="mb0" type="checkbox" name="ModuleFunction[]" value="*"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/modulefunctions','All functions');?></label><br>
<?php foreach ($functions as $key => $Function) : ?>
<label class="fs12"><input class="mb0" type="checkbox" name="ModuleFunction[]" value="<?php echo $key?>"> <?php echo htmlspecialchars($Function['explain'])?></label><br>
<?php endforeach; ?>
<br/>