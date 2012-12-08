<? foreach ($messages as $msg ) : ?>
<div class="message-row<?=$msg['user_id'] == 0 ? ' response' : ''?>"><div class="msg-date"><?=date('Y-m-d H:i:s',$msg['time']);?></div><span class="usr-tit"><?=$msg['user_id'] == 0 ? htmlspecialchars($chat->nick) : $msg['name_support'] ?>:</span> <?=nl2br(htmlspecialchars(trim($msg['msg'])));?></div>
<? endforeach; ?>