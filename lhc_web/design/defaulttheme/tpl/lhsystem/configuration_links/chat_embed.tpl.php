<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_embed_pre.tpl.php'));?>
<?php if ($system_configuration_links_chat_embed_enabled == true && $currentUser->hasAccessTo('lhsystem','generatejs')) : ?>
<div class="col-md-6">
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Live help embed code');?></h4>
			<ul>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
			</ul>
		</div>
<?php endif; ?>