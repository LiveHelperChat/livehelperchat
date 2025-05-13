<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/chat_embed_pre.tpl.php'));?>
<?php if ($system_configuration_links_chat_embed_enabled == true && $currentUser->hasAccessTo('lhsystem','generatejs')) : ?>
<div class="col-md-6">
			<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Live help embed code');?></h5>
			<ul>
                <li><a href="<?php echo erLhcoreClassDesign::baseurl('system/htmlcodebeta')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code (new)');?></a></li>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code (legacy)');?></a></li>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('system/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code (legacy)');?></a></li>
			</ul>
		</div>
<?php endif; ?>