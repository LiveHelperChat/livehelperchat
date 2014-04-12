<?php $departments = erLhcoreClassModelDepartament::getList(array('filter' => array('disabled' => 0, 'hidden' => 0)));
// Show only if there are more than 1 department
if (count($departments) > 1) : ?>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Department');?></label>
<select name="DepartamentID">
    <?php
    $departments = erLhcoreClassDepartament::sortByStatus($departments);
    foreach ($departments as $departament) : ?>
        <option <?php if ($departament->is_online === false) : ?>class="offline-dep"<?php endif;?> value="<?php echo $departament->id?>" <?php isset($input_data->departament_id) && $input_data->departament_id == $departament->id ? print 'selected="selected"' : '';?> ><?php echo htmlspecialchars($departament->name)?><?php if ($departament->is_online === false) : ?>&nbsp;&nbsp;--=<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Offline');?>=--<?php endif;?></option>
    <?php endforeach; ?>
</select>
<?php endif; ?>