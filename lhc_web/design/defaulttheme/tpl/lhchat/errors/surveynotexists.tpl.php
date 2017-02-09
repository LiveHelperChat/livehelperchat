<?php if (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'debug_output' ) == true) : ?>

<?php $errors = array(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatnotexists','Survey not exist please check embed code options'))?>

<?php if (isset($errors)) : ?>
         <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php else : ?>
<script>
$( document ).ready(function() {
	lhinst.userclosedchatembed();
});
</script>
<?php endif;?>