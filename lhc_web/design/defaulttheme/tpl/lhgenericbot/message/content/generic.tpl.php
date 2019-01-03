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
                                    <a target="_blank" <?php if (isset($itemButton['content']['payload_message']) && $itemButton['content']['payload_message'] != '') : ?>onclick='lhinst.buttonClicked(<?php echo json_encode($itemButton['content']['payload_message'])?>,<?php echo $messageId?>,$(this))'<?php else : ?>onclick="lhinst.enableVisitorEditor()"<?php endif;?> href="<?php echo htmlspecialchars($itemButton['content']['payload'])?>">
                                    <i class="material-icons">open_in_new</i>
                                    <?php elseif ($itemButton['type'] == 'updatechat') : ?>
                                    <a data-no-change="true" onclick='lhinst.updateChatClicked(<?php echo json_encode($itemButton['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
                                    <?php elseif ($itemButton['type'] == 'trigger') : ?>
                                    <a data-no-change="true" onclick='lhinst.updateTriggerClicked(<?php echo json_encode($itemButton['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
                                    <?php else : ?>
                                    <a data-no-change="true" onclick='lhinst.buttonClicked(<?php echo json_encode($itemButton['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
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