<section>
    <p class="title" data-section-title><a href="#panel3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Remarks')?></a></p>
    <div class="content" data-section-content>
      <div id="remarks-status-<?php echo $chat->id?>" class="icon-pencil pb10 success-color"></div>
      <div>
      	<textarea class="mh150" <?php if (!isset($hideActionBlock)) : ?>onkeyup="lhinst.saveRemarks('<?php echo $chat->id?>')"<?php else : ?>readonly<?php endif;?> id="ChatRemarks-<?php echo $chat->id?>"><?php echo htmlspecialchars($chat->remarks)?></textarea>
      </div>
    </div>
</section>