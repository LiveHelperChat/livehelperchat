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
    <input class="form-control" type="text" name="Username" value="" autocomplete="<?php echo $passkeyLoginAvailable ? 'username webauthn' : 'username'?>" />
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

    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div class="btn-group btn-group-sm text-nowrap" role="group" aria-label="Login">
            <button type="submit" class="btn btn-primary" name="Login" value="Login"><span class="material-icons me-1">login</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Login');?></button>
            <?php if ($passkeyLoginAvailable) : ?>
            <button type="button" class="btn btn-outline-secondary" data-lh-passkey-login data-options-url="<?php echo erLhcoreClassDesign::baseurl('user/passkeylogin')?>/options" data-verify-url="<?php echo erLhcoreClassDesign::baseurl('user/passkeylogin')?>/verify"><span class="material-icons me-1">passkey</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Login with a passkey');?></button>
            <?php endif;?>
            <a class="btn btn-outline-secondary" href="<?php echo erLhcoreClassDesign::baseurl('user/forgotpassword')?>"><span class="material-icons me-1">lock_reset</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Password reminder')?></a>
        </div>
        <div class="text-muted small fs11"><em title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Taken time');?> - <?php echo round((microtime(true) - erLhcoreClassLazyDatabaseConfiguration::$connectionStartTime) * 1000, 2)?> ms"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/login','Your IP');?>: <?php echo htmlspecialchars(erLhcoreClassIPDetect::getIP());?></em>
        <div class="hide"><br><em>Server: <?php echo time();?> / User: <script>document.write(Math.round((new Date()).getTime()/1000));</script></em></div></div>
    </div>

<?php if ($passkeyLoginAvailable) : ?>
<div class="text-danger small mt-1" id="lh-passkey-login-error"></div>
<?php endif;?>

<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect_url);?>" />

<?php $captchaAction = 'login_action'; ?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/recaptcha.tpl.php'));?>



</form>
