<section>
    <p class="title" data-section-title><a href="#panel3"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Notes')?></a></p>
    <div class="content" data-section-content>
      <div id="remarks-status-online-<?php echo $online_user->id?>" class="icon-pencil pb10 success-color"></div>
      <div>
      	<textarea class="mh150" onkeyup="lhinst.saveNotes('<?php echo $online_user->id?>')" id="OnlineRemarks-<?php echo $online_user->id?>"><?php echo htmlspecialchars($online_user->notes)?></textarea>
      </div>
    </div>
</section>