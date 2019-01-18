<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/request','Request permission');?></h1>

<?php if (isset($errors)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($requested) && $requested == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('permission/request','Permission requested'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('permission/request')?>" method="post" enctype="multipart/form-data">
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    <ul class="list-unstyled">
    <?php $counter = 0; foreach ($users as $user) : ?>
        <li><label><input <?php ($counter == 0) ? print 'checked="checked"' : ''?> type="radio" name="UserID" value="<?php echo $user->id?>"/> <?php echo htmlspecialchars($user)?></label></li>
    <?php endforeach;?>	
	</ul>
	<input type="hidden" value="<?php echo htmlspecialchars($permission)?>" name="Permissions" />
	<input type="submit" class="btn btn-default" name="RequestPermissionAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('permission/request','Request permissions');?>" />
</form>