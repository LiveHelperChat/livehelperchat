<section>
    <p class="title" data-section-title><a href="#screenshot"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Screenshot')?></a></p>
    <div class="content" data-section-content>    
      <div>
            
	    <ul class="button-group radius">
	      <li><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Take user screenshot')?>" class="button tiny" onclick="lhinst.addExecutionCommand('<?php echo $online_user->id?>','lhc_screenshot')" /></li>
		  <li><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Refresh')?>" class="button tiny" onclick="lhinst.updateScreenshotOnline('<?php echo $online_user->id?>')" /></li>
		</ul>
	
      	<div id="user-screenshot-container">
      	
      	<?php if ($online_user->screenshot !== false) : ?>    
      	<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Taken')?> <?php echo $online_user->screenshot->date_front?></h5>
      	   	
      	<a href="#" class="screnshot-container">
			<img id="screenshotImage" src="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $online_user->screenshot->id?>/<?php echo $online_user->screenshot->security_hash?>" alt="" />
		</a>
		<script>
		$(document).ready(function(){
		  $('.screnshot-container').zoom({callback: function(){
		        $(this).colorbox({'width':'95%','height':'95%',html: $('.screnshot-container').html()});
		    }});
		});
		</script>		
      	<?php else : ?>      
      	<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Empty...')?>
      	<?php endif;?>
      	
      	</div>
      </div>
    </div>
</section>