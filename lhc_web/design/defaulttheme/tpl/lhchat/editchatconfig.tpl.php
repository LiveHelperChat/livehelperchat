<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('systemconfig/edit','Edit')?></h1>

<?php if (isset($data_updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('systemconfig/edit','Data updated') ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>	
<?php endif; ?>

<form method="post" action="<?php echo erLhcoreClassDesign::baseurl('chat/editchatconfig')?>/<?php echo $systemconfig->identifier?>">

<p><?php echo htmlspecialchars($systemconfig->explain); ?></p>

<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('systemconfig/edit','Identifier');?></label>
<input class="default-input" type="text" disabled="disabled" value="<?php echo htmlspecialchars($systemconfig->identifier);?>" />

<?php if ( $systemconfig->type == erLhcoreClassModelChatConfig::SITE_ACCESS_PARAM_ON ) : ?>

    <?php foreach (erConfigClassLhConfig::getInstance()->getSetting('site','available_site_access') as $siteaccess) : 
    $siteaccessOptions = erConfigClassLhConfig::getInstance()->getSetting('site_access_options',$siteaccess); ?>
    <label><?=erTranslationClassLhTranslation::getInstance()->getTranslation('systemconfig/edit','Values applies to');?> - <?php echo htmlspecialchars($siteaccess);?></label>
    <input class="default-input" name="Value<?php echo $siteaccess?>" type="text" value="<?php isset($systemconfig->data[$siteaccess]) ? print htmlspecialchars($systemconfig->data[$siteaccess]) : ''?>" />
    <?php endforeach;?>
	
<?php else : ?>
    <input class="default-input" type="text" name="ValueParam" value="<?php echo htmlspecialchars($systemconfig->value);?>" />
<?php endif;?>

<input type="submit" class="button small" name="UpdateConfig" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('systemconfig/edit','Update')?>"/>

</form>