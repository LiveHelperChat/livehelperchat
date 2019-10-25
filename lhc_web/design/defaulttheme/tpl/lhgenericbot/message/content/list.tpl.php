<div class="list-group meta-message meta-message-<?php echo $messageId?>">
    <?php $listCompactStyle = $metaMessageData['options']['no_highlight'] == true; ?>

    <?php foreach ($metaMessage['items'] as $index => $item) : ?>
        <div class="list-group-element<?php $listCompactStyle == true ? print ' compact' : print ' large'?>">

            <?php $itemLink = $item; ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/part/link.tpl.php'));?>

            <?php if ($listCompactStyle == false && $index == 0 && $item['content']['img'] != '') : ?>
                    <div class="element-background" style="background-image: url('<?php echo $item['content']['img']?>')"></div>
            <?php endif ?>

            <div class="row element-description-row">
                <div class="col-9">
                    <div class="element-description">
                        <h4><?php echo $linkData['start']?><?php echo htmlspecialchars($item['content']['title'])?><?php echo $linkData['end']?></h4>
                        <div><?php echo htmlspecialchars($item['content']['subtitle'])?></div>
                        <?php if (isset($item['buttons']) && !empty($item['buttons'])) : ?>
                            <ul class="quick-replies list-inline">
                                <?php foreach ($item['buttons'] as $itemButton) : ?>
                                    <li class="list-inline-item">
                                        <?php if ($itemButton['type'] == 'url') : ?>
                                        <a class="btn btn-xs btn-secondary btn-bot" <?php if (isset($itemButton['content']['payload_message']) && $itemButton['content']['payload_message'] != '') : ?>onclick='lhinst.buttonClicked(<?php echo json_encode($itemButton['content']['payload_message'])?>,<?php echo $messageId?>,$(this))'<?php else : ?>onclick="lhinst.enableVisitorEditor()"<?php endif;?> target="_blank" href="<?php echo htmlspecialchars($itemButton['content']['payload'])?>">
                                        <i class="material-icons">open_in_new</i>
                                        <?php elseif ($itemButton['type'] == 'updatechat') : ?>
                                        <a class="btn btn-sm btn-secondary btn-bot" data-no-change="true" onclick='lhinst.updateChatClicked(<?php echo json_encode($itemButton['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
                                        <?php elseif ($itemButton['type'] == 'trigger') : ?>
                                        <a class="btn btn-sm btn-secondary btn-bot" data-no-change="true" onclick='lhinst.updateTriggerClicked(<?php echo json_encode($itemButton['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
                                        <?php else : ?>
                                        <a class="btn btn-sm btn-secondary btn-bot" data-no-change="true" onclick='lhinst.buttonClicked(<?php echo json_encode($itemButton['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
                                        <?php endif?>
                                        <?php echo htmlspecialchars($itemButton['content']['name'])?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-3">
                    <?php if ($item['content']['img'] != '' && ($index != 0 || $listCompactStyle == true)) : ?>
                        <?php echo $linkData['start']?><img class="float-right img-fluid" src="<?php echo $item['content']['img']?>" /><?php echo $linkData['end']?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (isset($metaMessage['list_quick_replies'])) : foreach ($metaMessage['list_quick_replies'] as $index => $item) : ?>
    <div class="list-group-element button-item <?php $listCompactStyle == true ? print ' compact' : print ' large'?>">
        <?php if ($item['type'] == 'url') : ?>
        <a target="_blank" <?php if (isset($item['content']['payload_message']) && $item['content']['payload_message'] != '') : ?>onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload_message'])?>,<?php echo $messageId?>,$(this))'<?php else : ?>onclick="lhinst.enableVisitorEditor()"<?php endif;?> href="<?php echo htmlspecialchars($item['content']['payload'])?>">
        <i class="material-icons">open_in_new</i>
        <?php elseif ($item['type'] == 'updatechat') : ?>
        <a data-no-change="true" onclick='lhinst.updateChatClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
        <?php elseif ($item['type'] == 'trigger') : ?>
        <a data-no-change="true" onclick='lhinst.updateTriggerClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
        <?php else : ?>
        <a data-no-change="true" onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,$(this),true)'>
        <?php endif?>
        <?php echo htmlspecialchars($item['content']['name'])?></a>
    </div>
    <?php endforeach;endif; ?>
</div>