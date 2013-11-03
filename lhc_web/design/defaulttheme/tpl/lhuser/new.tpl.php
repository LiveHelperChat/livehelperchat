<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','New user');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('user/new')?>" method="post" autocomplete="off" enctype="multipart/form-data">

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Username');?></label>
<input class="inputfield" type="text" name="Username" value="<?php echo htmlspecialchars($user->username);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','E-mail');?></label>
<input type="text" class="inputfield" name="Email" value="<?php echo htmlspecialchars($user->email);?>"/>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Password');?></label>
<input type="password" class="inputfield" name="Password" value=""/>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Repeat the new password');?></label>
<input type="password" class="inputfield" name="Password1" value=""/>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Name');?></label>
<input class="inputfield" type="text" name="Name" value="<?php echo htmlspecialchars($user->name);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Surname');?></label>
<input class="inputfield" type="text" name="Surname" value="<?php echo htmlspecialchars($user->surname);?>" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Photo');?>, (jpg,png)</label>
<input type="file" name="UserPhoto" value="" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','User group')?></label>
<?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'DefaultGroup[]',
                    'selected_id'    => $user->user_groups_id,
					'multiple' 		 => true,
                    'list_function'  => 'erLhcoreClassModelGroup::getList'
            )); ?>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Disabled')?>&nbsp;<input type="checkbox" value="on" name="UserDisabled" <?php echo $user->disabled == 1 ? 'checked="checked"' : '' ?> /></label>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Do not show user status as online')?>&nbsp;<input type="checkbox" value="on" name="HideMyStatus" <?php echo $user->hide_online == 1 ? 'checked="checked"' : '' ?> /></label>

<hr>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Departments')?></h5>

<label><input type="checkbox" value="on" name="all_departments" <?php echo $user->all_departments == 1 ? 'checked="checked"' : '' ?> /><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','All departments')?></label>

<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
    <label><input type="checkbox" name="UserDepartament[]" value="<?php echo $departament['id']?>"<?php echo in_array($departament['id'],$userdepartaments) ? 'checked="checked"' : '';?>/><?php echo htmlspecialchars($departament['name'])?></label>
<?php endforeach; ?>

<input type="submit" class="small button" name="Update_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Save');?>"/>

</form>

