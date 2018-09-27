<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhsurvey','redirect_to_survey')) : ?>
<a class="material-icons mr-0" onclick="lhc.revealModal({'url':WWW_DIR_JAVASCRIPT +'survey/choosesurvey/<?php echo $chat->id?>'})" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Redirect user to survey')?>">speaker_notes</a>
<?php endif; ?>