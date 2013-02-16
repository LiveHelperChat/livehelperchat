<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Fill out this form to start a chat');?></h1>
<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('chat/startchat')?>">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Name');?></label>
<input type="text" class="inputfield" name="Username" value="" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','E-mail');?></label>
<input type="text" class="inputfield" name="Email" value="" />

<?php 

$departments = erLhcoreClassDepartament::getDepartaments();

// Show only if there are more than 1 department
if (count($departments) > 1) : ?>
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Department');?></label>
<select name="DepartamentID">
    <?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) : ?>
        <option value="<?php echo $departament['id']?>"><?php echo $departament['name']?></option>
    <?php endforeach; ?>
</select>
<?php endif; ?> 

<input type="submit" class="small button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Start chat');?>" name="StartChat" />
<input type="hidden" value="<?php echo htmlspecialchars($referer);?>" name="URLRefer"/>

</form>