<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','take_screenshot')) : ?>
<section>
    <p class="title" data-section-title><a href="#screenshot"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Screenshot')?></a></p>
    <div class="content" data-section-content>    
      <div>
            
	    <ul class="button-group radius">
	      <li><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Take user screenshot')?>" class="button tiny" onclick="lhinst.addRemoteCommand('<?php echo $chat->id?>','lhc_screenshot')" /></li>
		  <li><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Refresh')?>" class="button tiny" onclick="lhinst.updateScreenshot('<?php echo $chat->id?>')" /></li>
		</ul>
	
      	<div id="user-screenshot-container">
      	
      	<?php if ($chat->screenshot !== false) : ?>    
      	<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/screenshot','Taken')?> <?php echo $chat->screenshot->date_front?></h5>
      	   	
      	<a href="#" class="screnshot-container">
			<img id="screenshotImage" src="<?php echo erLhcoreClassDesign::baseurl('file/downloadfile')?>/<?php echo $chat->screenshot->id?>/<?php echo $chat->screenshot->security_hash?>" alt="" />
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
<?php endif;?>