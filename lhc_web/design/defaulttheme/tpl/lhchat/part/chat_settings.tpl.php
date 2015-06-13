<div class="form-group">
<?php $systemconfig = erLhcoreClassModelChatConfig::fetch($attribute);?>

<?php if (!isset($boolValue)) : ?>
<label><?php print erTranslationClassLhTranslation::getInstance()->getTranslation('listchatconfig',$systemconfig->explain); ?></label>
<?php else : ?>
<label><input type="checkbox" name="<?php echo $attribute?>ValueParam" value="1" <?php if ($systemconfig->value == 1) : ?>checked="checked"<?php endif;?> /> <?php print erTranslationClassLhTranslation::getInstance()->getTranslation('listchatconfig',$systemconfig->explain); ?></label>
<?php endif; ?>

<?php if ( $systemconfig->type == erLhcoreClassModelChatConfig::SITE_ACCESS_PARAM_ON ) : ?>

    <?php foreach (erConfigClassLhConfig::getInstance()->getSetting('site','available_site_access') as $siteaccess) : 
    $siteaccessOptions = erConfigClassLhConfig::getInstance()->getSetting('site_access_options',$siteaccess); ?>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/editchatconfig','Value in');?> - &quot;<?php echo htmlspecialchars($siteaccess);?>&quot; <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/editchatconfig','site access');?></label>
    <input class="form-control" name="<?php echo $attribute?>Value<?php echo $siteaccess?>" type="text" value="<?php isset($systemconfig->data[$siteaccess]) ? print htmlspecialchars($systemconfig->data[$siteaccess]) : ''?>" />
    <?php endforeach;?>
	
<?php else : ?>
	
	<?php if (!isset($boolValue)) : ?>
    <input class="form-control" type="text" name="<?php echo $attribute?>ValueParam" value="<?php echo htmlspecialchars($systemconfig->value);?>" />
    <?php else : unset($boolValue);?> <?php endif;?>
<?php endif;?>
<?php if (isset($configExplain)) : ?>
<small><?php echo $configExplain?></small>
<?php unset($configExplain);endif;?>
</div>