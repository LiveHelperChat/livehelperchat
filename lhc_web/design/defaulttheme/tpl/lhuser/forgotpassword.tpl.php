<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','Password reminder');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('user/forgotpassword')?>">

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','E-mail')?>:</label>
    <input type="text" class="form-control form-control-sm" name="Email" value="" />
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/recaptcha.tpl.php'));?>

<input type="submit" class="btn btn-primary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/forgotpassword','Restore password')?>" name="Forgotpassword" />

</form>
