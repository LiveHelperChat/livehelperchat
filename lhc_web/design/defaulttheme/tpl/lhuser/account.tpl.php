<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Logged user');?> - <?php echo $user->name,' ',$user->surname?></h1>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($account_updated) && $account_updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>	
<?php endif; ?>


<div class="explain"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Do not enter password unless you want to change it');?></div>
<br />

<form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>" method="post" autocomplete="off">

    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Username');?></label>
    <input type="text" placeholder="Your username" name="Username" value="<?php echo htmlspecialchars($user->username);?>" />

    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Password');?></label>
    <input type="password" placeholder="Enter new password" name="Password" value=""/>
    	
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Repeat password');?></label>
    <input type="password" placeholder="Repeat new password" name="Password1" value=""/>
    	
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Email');?></label>
    <input type="text" value="<?php echo $user->email;?>" name="Email" placeholder="Your email address" id="email" class="required email valid">
    
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Name');?></label>
    <input type="text" name="Name" value="<?php echo htmlspecialchars($user->name);?>"/>
    	
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Surname');?></label>
    <input type="text" name="Surname" value="<?php echo htmlspecialchars($user->surname);?>"/>
        
    <ul class="button-group radius">
    <li><input type="submit" name="Update" class="small button" value="Update"></li>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('/')?>" class="small button">Return</a></li>
    </ul>
</form>
	
<hr>	
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Assigned departments');?></h5>
<?php if (isset($account_updated_departaments) && $account_updated_departaments == 'done') : ?>
	<div class="dataupdate"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Account updated');?></div>
	<br />
<?php endif; ?>

<?php if ($alldepartaments === true) :?>
<div class="alert-box success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','You have global right to see all departments chats');?></div>
<?php endif;?>

<?php $userDepartaments = erLhcoreClassUserDep::getUserDepartaments(); ?>

<?php if ($editdepartaments === true) { ?>
<form action="<?php echo erLhcoreClassDesign::baseurl('/user/account/')?>" method="post">
<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
    <label><input type="checkbox" name="UserDepartament[]" value="<?php echo $departament['id']?>" <?php echo in_array($departament['id'],$userDepartaments) ? 'checked="checked"' : '';?>/> <?php echo $departament['name']?></label><br />
<?php endforeach; ?>

<input type="submit" class="small button" name="UpdateDepartaments_account" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>"/>
</form>
<?php } else {?>
<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
    <label><input type="checkbox" disabled value="<?php echo $departament['id']?>"<?php echo in_array($departament['id'],$userDepartaments) ? 'checked="checked"' : '';?>/> <?php echo $departament['name']?></label><br />
<?php endforeach; ?>

<?php } ?>
</fieldset>

