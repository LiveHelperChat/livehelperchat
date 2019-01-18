<?php if ($online->screenshot !== false) : ?>     
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Taken')?> <?php echo $online->screenshot->date_front?></h5> 
<a href="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $online->screenshot->id?>/<?php echo $online->screenshot->security_hash?>" target="_blank" class="screnshot-container"><img id="screenshotImage" src="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $online->screenshot->id?>/<?php echo $online->screenshot->security_hash?>" alt="" /></a>
<?php else : ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Perhaps screenshot is under way or screenshot is not supported on client browser, click refresh to check for a screenshot')?>.</p>
<input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Refresh')?>" class="button tiny round" onclick="lhinst.updateScreenshotOnline('<?php echo $online->id?>')" />
<?php endif;?>