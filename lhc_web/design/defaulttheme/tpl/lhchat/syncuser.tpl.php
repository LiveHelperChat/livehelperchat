<?php foreach ($messages as $msg ) : ?>
<div class="message-row response"><div class="msg-date"><?php echo date('Y-m-d H:i',$msg['time']);?></div><span class="usr-tit"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Support')?>:</span> <?php echo nl2br(htmlspecialchars(trim($msg['msg'])));?></div>
<?php endforeach; ?>