<div class="meta-message meta-message-<?php echo $messageId?>" style="padding:10px">
    <div class="row">
        <div class="col-12">
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
                                    <a rel="noreferrer" target="_blank" <?php if (isset($itemButton['content']['payload_message']) && $itemButton['content']['payload_message'] != '') : ?>data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode($itemButton['content']['payload_message']. '__' . md5($itemButton['content']['name']))?> onclick='lhinst.buttonClicked(<?php echo json_encode($itemButton['content']['payload_message']. '__' . md5($itemButton['content']['name']))?>,<?php echo $messageId?>,$(this))'<?php else : ?>onclick="lhinst.enableVisitorEditor()"<?php endif;?> href="<?php echo htmlspecialchars($itemButton['content']['payload'])?>">
                                    <i class="material-icons"><?php if (isset($react) && $react == true) : ?>&#xf106;<?php else : ?>open_in_new<?php endif; ?></i>
                                    <?php elseif ($itemButton['type'] == 'updatechat') : ?>
                                    <a rel="noreferrer" data-no-change="true" data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode($itemButton['content']['payload'])?> data-keep="true" onclick='lhinst.updateChatClicked(<?php echo json_encode($itemButton['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
                                    <?php elseif ($itemButton['type'] == 'trigger') : ?>
                                    <a rel="noreferrer" data-no-change="true" data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode($itemButton['content']['payload']. '__' . md5($itemButton['content']['name']))?> data-keep="true" onclick='lhinst.updateTriggerClicked(<?php echo json_encode($itemButton['content']['payload']. '__' . md5($itemButton['content']['name']))?>,<?php echo $messageId?>,$(this),true)'>
                                    <?php else : ?>
                                    <a rel="noreferrer" data-no-change="true" data-id="<?php echo $messageId?>" data-payload=<?php echo json_encode($itemButton['content']['payload']. '__' . md5($itemButton['content']['name']))?> data-keep="true" onclick='lhinst.buttonClicked(<?php echo json_encode($itemButton['content']['payload']. '__' . md5($itemButton['content']['name']))?>,<?php echo $messageId?>,$(this),true)'>
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
    </div>
</div>