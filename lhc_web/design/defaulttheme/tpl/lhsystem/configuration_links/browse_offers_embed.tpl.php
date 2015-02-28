<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/browse_offers_embed_pre.tpl.php'));?>	
<?php if ($system_configuration_links_browse_offers_embed_enabled == true && $currentUser->hasAccessTo('lhbrowseoffer','manage_bo')) : ?>
<div class="col-md-6">
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Browse offers embed code');?></h4>
			<ul>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('browseoffer/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Embed code');?></a></li>
			</ul>
		</div>
<?php endif; ?>