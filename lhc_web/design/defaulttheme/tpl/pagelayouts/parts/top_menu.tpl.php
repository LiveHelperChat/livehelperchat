<?php $currentUser = erLhcoreClassUser::instance(); ?>
<nav class="top-bar">
	<ul class="title-area">
		<li class="name">
			<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/page_head_logo_back_office.tpl.php'));?>
		</li>
		<li class="toggle-topbar menu-icon"><a href="#"><span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Menu');?></span></a></li>
	</ul>
	<section class="top-bar-section">
		<ul class="right">
			<li><a href="javascript:void(0)" onclick="javascript:lhinst.chatTabsOpen()"><i class="icon-chat"></i></a></li>
			<li class="divider"></li>
			<li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/lists')?>" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chats list');?></a></li>
			<?php if ($currentUser->hasAccessTo('lhchat','use_onlineusers')) : ?>
			<li><a href="<?php echo erLhcoreClassDesign::baseurl('chat/onlineusers')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Online visitors');?></a></li>
			<?php endif;?>
			<li class="divider"></li>
			<?php if ($currentUser->hasAccessTo('lhquestionary','manage_questionary')) : ?>
			<li><a href="<?php echo erLhcoreClassDesign::baseurl('questionary/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Questionary');?></a></li>
			<?php endif;?>
			<?php if ($currentUser->hasAccessTo('lhfaq','manage_faq')) : ?>
			<li><a href="<?php echo erLhcoreClassDesign::baseurl('faq/list')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','FAQ');?></a></li>
			<?php endif;?>
			<?php if ($currentUser->hasAccessTo('lhchatbox','manage_chatbox')) : ?>
			<li><a href="<?php echo erLhcoreClassDesign::baseurl('chatbox/configuration')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Chatbox');?></a></li>
			<?php endif; ?>			
			<?php if ($currentUser->hasAccessTo('lhbrowseoffer','manage_bo')) : ?>
			<li><a href="<?php echo erLhcoreClassDesign::baseurl('browseoffer/index')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Browse offers');?></a></li>
			<?php endif; ?>			
			<li class="divider"></li>
			<?php if ($currentUser->hasAccessTo('lhsystem','use')) : ?>
			<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/configuration')?>"><i class="icon-tools"></i></a></li>
			<?php endif; ?>
			<?php $hideULSetting = true;?>
			<?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>
			<?php include_once(erLhcoreClassDesign::designtpl('pagelayouts/parts/user_box.tpl.php'));?>
		</ul>
	</section>
</nav>
