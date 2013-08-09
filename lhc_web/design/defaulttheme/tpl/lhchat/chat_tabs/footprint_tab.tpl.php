<?php if ( erLhcoreClassModelChatConfig::fetch('track_footprint')->current_value == 1) : ?>
<section>
	<p class="title" data-section-title>
		<a href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Footprint')?></a>
	</p>
	<div class="content" data-section-content>
		<?php include(erLhcoreClassDesign::designtpl('lhchat/footprint.tpl.php'));?>
	</div>
</section>
<?php endif;?>