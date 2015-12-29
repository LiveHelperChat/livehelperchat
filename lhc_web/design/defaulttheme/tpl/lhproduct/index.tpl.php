<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhproduct/index','Product');?></h1>

<form action="" method="post">

    <?php $attribute = 'product_enabled_module'; $boolValue = true; ?>
    <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>

    <input type="submit" class="btn btn-default" name="UpdateProductModule" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>" />

</form>

<hr>

<ul>
	<li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/Product"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Products');?></a></li>
</ul>
