<? foreach ($messages as $msg ) : ?>
<div class="message-row response"><div class="msg-date"><?=date('Y-m-d H:i',$msg['time']);?></div><span class="usr-tit"><?=erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Support')?>:</span> <?=nl2br(htmlspecialchars(trim($msg['msg'])));?></div>
<? endforeach; ?>