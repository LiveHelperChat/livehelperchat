<?php if ($currentUser->hasAccessTo('lhchat','administratechatpriority')) : ?>
<li><a href="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/ChatPriority"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chat priority');?></a></li>
<?php endif; ?>