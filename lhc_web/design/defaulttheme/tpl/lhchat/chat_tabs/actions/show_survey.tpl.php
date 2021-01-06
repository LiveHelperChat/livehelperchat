<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhsurvey','redirect_to_survey')) : ?>
<div class="col-6 pb-1">
    <a class="text-muted" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'survey/choosesurvey/<?php echo $chat->id?>'})"><span class="material-icons">speaker_notes</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Redirect user to survey')?></a>
</div>
<?php endif; ?>