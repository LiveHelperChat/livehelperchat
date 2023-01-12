<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Please login');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($logout_reason)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhuser/logout_reason.tpl.php'));?>
<?php endif;?>

<?php if (isset($session_ended)) : ?>
    <?php $errors = array(erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Your session has ended. Please login!')); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif;?>

<form id="form-start-chat" method="post" action="<?php echo erLhcoreClassDesign::baseurl('user/login')?>">

<?php if (isset($crossdomain) && $crossdomain == true) : ?>
    <input type="hidden" name="cookie" value="crossdomain" />
<?php endif; ?>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Username OR E-mail');?></label>
    <input class="form-control" type="text" name="Username" value="" />
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Password');?></label>
    <input type="password" class="form-control" name="Password" value="" />
</div>

<div class="form-group">
    <label class="mb-1"><input class="input-checkbox me-1" type="checkbox" name="rememberMe" value="1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Remember me');?></label>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhuser/oauth_login_multiinclude_tab.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="btn-group btn-group-sm" role="group" aria-label="Login">
        <input type="submit" class="btn btn-primary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Login');?>" name="Login" />
        <a class="btn btn-outline-secondary" href="<?php echo erLhcoreClassDesign::baseurl('user/forgotpassword')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Password reminder')?></a>
    </div>





<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect_url);?>" />

<?php include(erLhcoreClassDesign::designtpl('lhkernel/recaptcha.tpl.php'));?>

</form>
