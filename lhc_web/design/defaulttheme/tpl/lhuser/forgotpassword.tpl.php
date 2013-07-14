<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','Password reminder');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('user/forgotpassword')?>">
<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','E-mail')?>:</label>
<input type="text" class="inputfield" name="Email" value="" />

<input type="submit" class="small round button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','Restore password')?>" name="Forgotpassword" />
</form>
