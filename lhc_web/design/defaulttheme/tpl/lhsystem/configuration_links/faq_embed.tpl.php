<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/faq_embed_pre.tpl.php'));?>	
<?php if ($system_configuration_links_faq_embed_enabled == true && $currentUser->hasAccessTo('lhfaq','manage_faq')) : ?>
<div class="col-md-6">
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','FAQ embed code');?></h4>
			<ul>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('faq/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('faq/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
			</ul>
		</div>
<?php endif; ?>