<div role="tabpanel" class="tab-pane" id="screenshot"> 

<div class="btn-group" role="group" aria-label="...">
  <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Take user screenshot')?>" class="btn btn-default" onclick="lhinst.addExecutionCommand('<?php echo $online_user->id?>','lhc_screenshot')" />
  <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Refresh')?>" class="btn btn-default" onclick="lhinst.updateScreenshotOnline('<?php echo $online_user->id?>')" />
</div>

<div id="user-screenshot-container">
<?php if ($online_user->screenshot !== false) : ?>    
<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Taken')?> <?php echo $online_user->screenshot->date_front?></h5>
   	
<a href="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $online_user->screenshot->id?>/<?php echo $online_user->screenshot->security_hash?>/(inline)/true" target="_blank" class="screnshot-container">
	<img id="screenshotImage" src="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $online_user->screenshot->id?>/<?php echo $online_user->screenshot->security_hash?>" alt="" />
</a>
<?php else : ?>      
<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Empty...')?>
<?php endif;?>      	
</div>

</div>