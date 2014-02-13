<section>
    <p class="title" data-section-title><a href="#screenshot"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Screenshots')?></a></p>
    <div class="content" data-section-content>    
      <div>
      	<input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('files/files','Make screenshot of user screen')?>" class="button tiny round" onclick="lhinst.addRemoteCommand('<?php echo $chat->id?>','lhc_screenshot')" />
      	<p id="user-screenshot-container">Pending...</p>
      </div>
    </div>
</section>