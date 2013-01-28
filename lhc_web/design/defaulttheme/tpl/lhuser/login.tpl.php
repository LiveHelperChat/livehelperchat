<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Please login');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('user/login')?>">

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Username');?></label>
<input type="text" name="Username" value="" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Password');?></label>
<input type="password" class="inputfield" name="Password" value="" />

<ul class="button-group radius">
<li><input type="submit" class="small button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Login');?>" name="Login" /></li>
<li><a class="small button" href="<?php echo erLhcoreClassDesign::baseurl('user/forgotpassword')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Password remind')?></a></li>
</ul>

</form>