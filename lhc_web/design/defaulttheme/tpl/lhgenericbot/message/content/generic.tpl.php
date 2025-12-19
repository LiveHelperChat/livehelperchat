<div class="meta-message meta-message-<?php echo $messageId?>" style="padding:10px">
    <div class="generic-carousel">
    <?php foreach ($metaMessage['items'] as $index => $item) : ?>
    <div class="generic-bubble-item">
        <div class="generic-bubble-content">

            <?php $itemLink = $item; ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/part/link.tpl.php'));?>

            <?php if ($item['content']['img'] != '') : ?>
                <?php echo $linkData['start']?><img class="img-fluid" src="<?php echo $item['content']['img']?>" /><?php echo $linkData['end']?>
            <?php endif ?>

            <h4><?php echo $linkData['start']?><?php echo htmlspecialchars($item['content']['title'])?><?php echo $linkData['end']?></h4>
            <p><?php echo htmlspecialchars($item['content']['subtitle'])?></p>

            <?php if (isset($item['buttons']) && !empty($item['buttons'])) : ?>
                <ul class="bot-btn-list">
                    <?php foreach ($item['buttons'] as $itemButton) : ?>
                        <li>
                            <?php if ($itemButton['type'] == 'url') : ?>
                            <a rel="noreferrer" target="_blank" data-no-msg="true" <?php if (isset($itemButton['content']['payload_message']) && $itemButton['content']['payload_message'] != '') : ?>data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode($itemButton['content']['payload_message']. '__' . substr(md5($itemButton['content']['name']),0,16))?> onclick='lhinst.buttonClicked(<?php echo json_encode(substr(md5($itemButton['content']['payload_message']),0,16) . '__' . substr(md5($itemButton['content']['name']),0,16))?>,<?php echo $messageId?>,$(this))'<?php endif;?> href="<?php echo htmlspecialchars($itemButton['content']['payload'])?>">
                            <i class="material-icons"><?php if (isset($react) && $react == true) : ?>&#xf106;<?php else : ?>open_in_new<?php endif; ?></i>
                            <?php elseif ($itemButton['type'] == 'updatechat') : ?>
                            <a rel="noreferrer" data-no-change="true" class="btn-link action-image <?php if (isset($metaMessageData['ch']) && in_array($itemButton['content']['payload'],$metaMessageData['ch'])) : ?>visited<?php endif;?>" <?php if (isset($itemButton['content']['no_name']) && $itemButton['content']['no_name'] == true) : ?>data-no-msg="true"<?php endif; ?> data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode(substr(md5($itemButton['content']['payload']),0,16))?> data-keep="true" onclick='lhinst.updateChatClicked(<?php echo json_encode(substr(md5($itemButton['content']['payload']),0,16))?>,<?php echo $messageId?>,$(this),true)'>
                            <?php elseif ($itemButton['type'] == 'trigger') : ?>
                            <a rel="noreferrer" data-no-change="true" class="btn-link action-image <?php if (isset($metaMessageData['ch']) && in_array(substr(md5($itemButton['content']['name']),0,16),$metaMessageData['ch'])) : ?>visited<?php endif;?>" <?php if (isset($itemButton['content']['no_name']) && $itemButton['content']['no_name'] == true) : ?>data-no-msg="true"<?php endif; ?> data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode(substr(md5($itemButton['content']['payload']),0,16). '__' . substr(md5($itemButton['content']['name']),0,16))?> data-keep="true" onclick='lhinst.updateTriggerClicked(<?php echo json_encode(substr(md5($itemButton['content']['payload']),0,16) . '__' . substr(md5($itemButton['content']['name']),0,16))?>,<?php echo $messageId?>,$(this),true)'>
                            <?php else : ?>
                            <a rel="noreferrer" data-no-change="true" class="btn-link action-image <?php if (isset($metaMessageData['ch']) && in_array(substr(md5($itemButton['content']['name']),0,16),$metaMessageData['ch'])) : ?>visited<?php endif;?>" <?php if (isset($itemButton['content']['no_name']) && $itemButton['content']['no_name'] == true) : ?>data-no-msg="true"<?php endif; ?> data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode(substr(md5($itemButton['content']['payload']),0,16). '__' . substr(md5($itemButton['content']['name']),0,16))?> data-keep="true" onclick='lhinst.buttonClicked(<?php echo json_encode(substr(md5($itemButton['content']['payload']),0,16) . '__' . substr(md5($itemButton['content']['name']),0,16))?>,<?php echo $messageId?>,$(this),true)'>
                            <?php endif?>

                            <?php echo htmlspecialchars($itemButton['content']['name'])?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
</div>