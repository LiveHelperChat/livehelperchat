<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Edit');?> - <?php echo htmlspecialchars($object_trans['name'])?></h1>


<form enctype="multipart/form-data" action="<?php echo erLhcoreClassDesign::baseurl('abstract/edit')?>/<?php echo $identifier?>/<?php echo $object->id?>" method="post">
	<?php include_once(erLhcoreClassDesign::designtpl('lhabstract/abstract_form.tpl.php'));?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
</form>



