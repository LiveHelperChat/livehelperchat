<?php 

$filter = array('filter' => array('disabled' => 0, 'hidden' => 0));

if (isset($input_data->departament_id_array)){
	$filter['filterin']['id'] = $input_data->departament_id_array;
}

$departments = erLhcoreClassModelDepartament::getList($filter);

// Show only if there are more than 1 department
if (count($departments) > 1) : $hasExtraField = true;?>

<?php if (isset($input_data->departament_id_array)) : foreach ($input_data->departament_id_array as $definedDep) : ?>
<input type="hidden" name="DepartmentIDDefined[]" value="<?php echo $definedDep?>" />
<?php endforeach;endif;?>


<div class="form-group<?php if (isset($errors['department'])) : ?> has-error<?php endif;?>">
    <label class="control-label">
    <?php if (isset($theme) && $theme !== false && $theme->department_title != '') : ?>
        <?php echo htmlspecialchars($theme->department_title)?>
    <?php else : ?>
        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Department');?>
    <?php endif;?>
    </label>
    
    <select class="form-control" name="DepartamentID" id="id_DepartamentID">
        <?php if (isset($theme) && $theme !== false && $theme->department_select != '') : ?>
            <option value="-1"><?php echo htmlspecialchars($theme->department_select)?></option>
        <?php endif;?>
        <?php
        $departments = erLhcoreClassDepartament::sortByStatus($departments);
        foreach ($departments as $departament) :  
        $isOnline = erLhcoreClassChat::isOnline($departament->id,false,array('ignore_user_status'=> (int)erLhcoreClassModelChatConfig::fetch('ignore_user_status')->current_value, 'online_timeout' => (int)erLhcoreClassModelChatConfig::fetch('sync_sound_settings')->data['online_timeout'])); ?>
            <?php if (($departament->visible_if_online == 1 && $isOnline === true) || $departament->visible_if_online == 0) : ?>
            <option data-attr-online="<?php if ($isOnline === false) : ?>false<?php else : ?>true<?php endif;?>" <?php if ($isOnline === false) : ?>class="offline-dep"<?php endif;?> value="<?php echo $departament->id?>" <?php isset($input_data->departament_id) && $input_data->departament_id == $departament->id ? print 'selected="selected"' : '';?> ><?php echo htmlspecialchars($departament->name)?><?php if ($isOnline === false) : ?>&nbsp;&nbsp;--=<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Offline');?>=--<?php endif;?></option>
            <?php endif;?>
        <?php endforeach; ?>
    </select>   
</div>
 <?php endif; ?>