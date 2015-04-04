<?php include(erLhcoreClassDesign::designtpl('lhabstract/parts/abstract_edit_title.tpl.php'));?>

<form enctype="multipart/form-data" action="" method="post">
		
	<?php if (!isset($custom_form)) : ?>
		<?php include(erLhcoreClassDesign::designtpl('lhabstract/abstract_form.tpl.php'));?>
	<?php else : ?>
		<?php include(erLhcoreClassDesign::designtpl("lhabstract/custom/".$custom_form));?>
	<?php endif;?>
		
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
</form>



