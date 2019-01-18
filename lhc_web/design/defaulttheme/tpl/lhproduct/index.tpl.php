<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('lhproduct/index','Products');?></h1>

<form action="" method="post">
    <div class="row">
        <div class="col-xs-6">
            <?php $attribute = 'product_enabled_module'; $boolValue = true; ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
            
            <?php $attribute = 'product_show_departament'; $boolValue = true; ?>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/part/chat_settings.tpl.php'));?>
            
            <input type="submit" class="btn btn-default" name="UpdateProductModule" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Update');?>" />
        </div>
        <div class="col-xs-6">
            <ul>
            	<li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/Product"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('theme/index','Products');?></a></li>
            </ul>
        </div>
    </div>
</form>