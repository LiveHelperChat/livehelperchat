<label class="fs12"><input class="mb-0" type="checkbox" name="ModuleFunction[]" value="*"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/modulefunctions','All functions');?></label><br>
<?php foreach ($functions as $key => $Function) : ?>
<label class="fs12"><input class="mb-0" type="checkbox" name="ModuleFunction[]" value="<?php echo $key?>"> <?php echo htmlspecialchars($Function['explain'])?> (<i><?php echo $key?></i>)</label><br>
<?php endforeach; ?>
<br/>