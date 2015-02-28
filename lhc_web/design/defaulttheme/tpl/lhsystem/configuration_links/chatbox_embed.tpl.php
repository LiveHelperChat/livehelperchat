<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chatbox_embed_pre.tpl.php'));?>	
<?php if ($system_configuration_links_chatbox_embed_enabled == true && $currentUser->hasAccessTo('lhchatbox','manage_chatbox')) : ?>
<div class="col-md-6">
	<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Chatbox embed code');?></h4>
	<ul>
		<li><a href="<?php echo erLhcoreClassDesign::baseurl('chatbox/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
		<li><a href="<?php echo erLhcoreClassDesign::baseurl('chatbox/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
	</ul>
</div>
<?php endif; ?>