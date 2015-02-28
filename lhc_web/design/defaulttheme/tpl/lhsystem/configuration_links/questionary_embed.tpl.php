<?php include(erLhcoreClassDesign::designtpl('lhsystem/configuration_links/questionary_embed_pre.tpl.php'));?>	
<?php if ($system_configuration_links_questionary_embed_enabled == true && $currentUser->hasAccessTo('lhquestionary','manage_questionary')) : ?>
<div class="col-md-6">
			<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/configuration','Questionary embed code');?></h4>
			<ul>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('questionary/htmlcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Widget embed code');?></a></li>
				<li><a href="<?php echo erLhcoreClassDesign::baseurl('questionary/embedcode')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Page embed code');?></a></li>
			</ul>
		</div>
<?php endif; ?>