<div class="message-row chatbox-row"><span class="usr-tit radius" data-sender="<?php echo htmlspecialchars($msg->name_support)?>"><?php echo htmlspecialchars($msg->name_support)?><div class="msg-date"><?php if (date('Ymd') == date('Ymd',$msg->time)) {	echo  date('H:i:s',$msg->time);} else { echo date('Y-m-d H:i:s',$msg->time);}; ?></div></span><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg->msg))?></div>
{{SPLITTER}}
<div class="message-row-in"><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($msg->msg))?></div>
