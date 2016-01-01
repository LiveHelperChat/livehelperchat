<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/paidchat_pre.tpl.php'));?>
<?php if ($system_configuration_paid_chat_enabled == true && $currentUser->hasAccessTo('lhpaidchat','use_admin')) : ?>
    <li><a href="<?php echo erLhcoreClassDesign::baseurl('paidchat/settings')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Paid chat configuration');?></a></li>
<?php endif; ?>