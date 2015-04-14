<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_screenshot_pre.tpl.php')); ?>
<?php if ($operator_screenshot_enabled == true && erLhcoreClassUser::instance()->hasAccessTo('lhchat','take_screenshot')) : ?>
<div role="tabpanel" class="tab-pane<?php if ($chatTabsOrderDefault == 'operator_screenshot_tab') print ' active';?>" id="main-user-info-screenshot-<?php echo $chat->id?>">
    <div class="btn-group" role="group" aria-label="...">
      <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Take user screenshot')?>" class="btn btn-default" onclick="lhinst.addRemoteCommand('<?php echo $chat->id?>','lhc_screenshot')" />
      <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Refresh')?>" class="btn btn-default" onclick="lhinst.updateScreenshot('<?php echo $chat->id?>')" />
    </div>
       
    <div id="user-screenshot-container">
      	<?php if ($chat->screenshot !== false) : ?>    
      	<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Taken')?> <?php echo $chat->screenshot->date_front?></h5>
      	   	
      	<a href="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $chat->screenshot->id?>/<?php echo $chat->screenshot->security_hash?>/(inline)/true" target="_blank" class="screnshot-container">
    		<img id="screenshotImage" src="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $chat->screenshot->id?>/<?php echo $chat->screenshot->security_hash?>" alt="" />
    	</a>
    	<script>
    	$(document).ready(function(){
    	  $('.screnshot-container').zoom();
    	});
    	</script>		
      	<?php else : ?>    
      	<br/>  
      	<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Empty...')?>
      	<?php endif;?>
    </div>
</div>
<?php endif;?>