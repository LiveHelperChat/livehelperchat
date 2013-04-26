<h1><?php echo htmlspecialchars($object_trans['name'])?></h1>

<form method="post" enctype="multipart/form-data" action="<?php echo erLhcoreClassDesign::baseurl('abstract/new')?>/<?php echo $identifier?>">
	<?php include_once(erLhcoreClassDesign::designtpl('lhabstract/abstract_form.tpl.php'));?>
</form>

