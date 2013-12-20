<section>
	<p class="title" data-section-title>
		<a href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Map')?></a>
	</p>
	<div class="content" data-section-content>
		<?php if ($chat->lat != 0 && $chat->lon) : ?>
		<a target="_blank" href="//maps.google.com/maps?t=h&q=loc:<?php echo $chat->lat?>,<?php echo $chat->lon?>&z=17&hl=en&z=11&t=m"><img src="//maps.google.com/maps/api/staticmap?zoom=13&size=400x300&maptype=roadmap&center=<?php echo $chat->lat?>,<?php echo $chat->lon?>&sensor=false&markers=color:green|<?php echo $chat->lat?>,<?php echo $chat->lon?>" alt="" title="<?php echo $chat->lat?>,<?php echo $chat->lon?>" /></a>
		<?php else : ?>
		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Could not detect. Make sure that GEO detection is enabled.')?></p>
		<?php endif;?>
	</div>
</section>