<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Please login');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($logout_reason)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhuser/logout_reason.tpl.php'));?>
<?php endif;?>

<form id="form-start-chat" method="post" action="<?php echo erLhcoreClassDesign::baseurl('user/login')?>">

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Username');?></label>
    <input class="form-control" type="text" name="Username" value="" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Password');?></label>
    <input type="password" class="form-control" name="Password" value="" />
</div>

<div class="form-group">
    <label class="mb6"><input class="input-checkbox" type="checkbox" name="rememberMe" value="1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Remember me');?></label>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<input type="submit" class="btn btn-primary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Login');?>" name="Login" />&nbsp;<a class="btn btn-default" href="<?php echo erLhcoreClassDesign::baseurl('user/forgotpassword')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Password reminder')?></a>

<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect_url);?>" />

</form>