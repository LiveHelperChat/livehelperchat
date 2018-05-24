<?php if (isset($async_call) && $async_call == true) : ?>
<script>lhinst.setDelay(<?php echo $msg['id']?>,<?php echo $metaMessage['duration']?>);</script>
<div class="msg-body hide"><?php if (isset($metaMessage['text'])) : ?><?php echo htmlspecialchars($metaMessage['text'])?><?php else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncuser','Typing...');?><?php endif; ?></div>
<?php endif; ?>