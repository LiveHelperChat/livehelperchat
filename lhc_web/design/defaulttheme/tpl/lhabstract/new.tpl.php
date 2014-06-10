<h1><?php echo htmlspecialchars($object_trans['name'])?></h1>

<form method="post" enctype="multipart/form-data" action="<?php echo erLhcoreClassDesign::baseurl('abstract/new')?>/<?php echo $identifier?>">
	
	<?php if (!isset($custom_form)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhabstract/abstract_form.tpl.php'));?>
	<?php else : ?>
		<?php include(erLhcoreClassDesign::designtpl("lhabstract/custom/".$custom_form));?>
	<?php endif;?>
	
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
</form>

