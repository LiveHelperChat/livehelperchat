<?php $errors = array(erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/nopermission','You do not have permission to edit selected object'))?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>

<a class="button round secondary tiny" href="javascript:window.history.back()"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/nopermission','Go back')?></a>