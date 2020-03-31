<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/proactive_pre.tpl.php'));?>
<?php if ($system_configuration_proactive_enabled == true && $currentUser->hasAccessTo('lhchat','administratecampaigs')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/ProactiveChatCampaign"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Pro active chat campaigns');?></a></li>
<?php endif;?>