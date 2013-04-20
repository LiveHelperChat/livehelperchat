<?php $currentUser = erLhcoreClassUser::instance(); ?>
<nav class="top-bar">
	<section class="top-bar-section">
	            <ul class="left">
	              	<li><a href="javascript:void(0)" onclick="javascript:lhinst.chatTabsOpen()"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chat rooms');?></a></li>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/lists')?>" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats lists');?></a></li>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/onlineusers')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Online users');?></a></li>
				<?php if ($currentUser->hasAccessTo('lhquestionary','manage_questionary')) : ?>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('questionary/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Questionary');?></a></li>
				<?php endif;?>
				<?php if ($currentUser->hasAccessTo('lhfaq','manage_faq')) : ?>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('faq/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','FAQ');?></a></li>
				<?php endif;?>
				<?php if ($currentUser->hasAccessTo('lhsystem','use')) : ?>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Configuration');?></a></li>
				<?php endif; ?>
	            </ul>
	</section>
</nav>