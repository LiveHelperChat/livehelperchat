<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','New group');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('user/newgroup')?>" method="post">

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Title');?></label>
    <input type="text" name="Name" class="form-control" value="" />
</div>
        
<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Member of this group can work with the following groups');?></label>
    <select multiple="multiple" size="5" class="form-control" name="MemberGroup[]">
        <?php 
        
        $assignedGrupsIds = array();
        
        if (isset($_POST['MemberGroup'])) {
            $assignedGrupsIds = $_POST['MemberGroup']; 
        }
        
        foreach (erLhcoreClassModelGroup::getList() as $groupMember) : ?>
            <option value="<?php echo $groupMember->id?>"<?php in_array($groupMember->id, $assignedGrupsIds) ? print ' selected="selected" ' : ''?>><?php echo htmlspecialchars($groupMember->name)?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="form-group">
  <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/editgroup','Disabled');?> <input type="checkbox" name="Disabled"  value="on" <?php echo $group->disabled == 1 ? 'checked="checked"' : ''?> /></label>
</div>

<div class="btn-group" role="group" aria-label="...">
	<input type="submit" class="btn btn-default" name="Save_group" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Save');?>"/>
    <input type="submit" class="btn btn-default" name="Save_group_and_assign_user" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/newgroup','Save and assign the user');?>"/>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

</form>
