<section>
    <p class="title" data-section-title><a href="#screenshot"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Screenshots')?></a></p>
    <div class="content" data-section-content>    
      <div>
      
      <input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Take user screenshot')?>" class="button tiny round" onclick="lhinst.addRemoteCommand('<?php echo $chat->id?>','lhc_screenshot')" />
	
      	<p id="user-screenshot-container">
      	
      	<?php if ($chat->screenshot !== false) : ?>      	
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
      	Empty...
      	<?php endif;?>
      	
      	</p>
      </div>
    </div>
</section>