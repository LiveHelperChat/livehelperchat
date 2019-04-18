<?php if ($chat->screenshot !== false) : ?>     
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Taken')?> <?php echo $chat->screenshot->date_front?></h4> 
<a href="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $chat->screenshot->id?>/<?php echo $chat->screenshot->security_hash?>/(inline)/true" target="_blank" class="screnshot-container"><img id="screenshotImage" src="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $chat->screenshot->id?>/<?php echo $chat->screenshot->security_hash?>" alt="" /></a>
<script>
$('.screnshot-container').zoom();
</script>		
<?php else : ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Screenshot will show if supported')?>.</p>
<input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Refresh')?>" class="btn btn-secondary btn-sm" onclick="lhinst.updateScreenshot('<?php echo $chat->id?>')" />
<?php endif;?>