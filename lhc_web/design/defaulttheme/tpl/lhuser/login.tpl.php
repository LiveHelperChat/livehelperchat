<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Please login');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form id="form-start-chat" method="post" action="<?php echo erLhcoreClassDesign::baseurl('user/login')?>">

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Username');?></label>
<input type="text" name="Username" value="" />

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Password');?></label>
<input type="password" class="inputfield" name="Password" value="" />

<label class="mb6"><input class="input-checkbox" type="checkbox" name="rememberMe" value="1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Remember me');?></label>

<input type="submit" class="small round button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Login');?>" name="Login" />&nbsp;<a class="fs11" href="<?php echo erLhcoreClassDesign::baseurl('user/forgotpassword')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Password reminder')?></a>

<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect_url);?>" />

</form>