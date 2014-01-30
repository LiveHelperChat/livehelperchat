<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/timezone','Live Helper Chat update');?></h1>

<?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/smtp','Settings updated'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<div id="database-status">

</div>

<input type="button" class="button small radius" value="Check for datatabase update" />
<script>



https://api.github.com/repos/remdex/livehelperchat/contents/lhc_web/doc/update_db

</script>