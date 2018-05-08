<div class="list-group">
    <?php $listCompactStyle = $metaMessageData['options']['no_highlight'] == true; ?>

    <?php foreach ($metaMessage['items'] as $index => $item) : ?>
        <div class="list-group-element<?php $listCompactStyle == true ? print ' compact' : print ' large'?>">

            <?php if ($listCompactStyle == false && $index == 0 && $item['content']['img'] != '') : ?>
                    <div class="element-background" style="background-image: url('<?php echo $item['content']['img']?>')"></div>
            <?php endif ?>

            <div class="row element-description-row">
                <div class="col-xs-9">
                    <div class="element-description">
                        <h4><?php echo htmlspecialchars($item['content']['title'])?></h4>
                        <div><?php echo htmlspecialchars($item['content']['subtitle'])?></div>
                        <?php if (isset($item['buttons']) && !empty($item['buttons'])) : ?>
                            <ul class="quick-replies list-inline">
                                <?php foreach ($item['buttons'] as $itemButton) : ?>
                                    <li>
                                        <?php if ($item['type'] == 'url') : ?>
                                        <a class="btn btn-xs btn-info" target="_blank" href="<?php echo htmlspecialchars($itemButton['content']['payload'])?>">
                                            <i class="material-icons">open_in_new</i>
                                            <?php elseif ($itemButton['type'] == 'updatechat') : ?>
                                            <a class="btn btn-xs btn-info" onclick='lhinst.updateChatClicked(<?php echo json_encode($itemButton['content']['payload'])?>,<?php echo $messageId?>,true)'>
                                            <?php else : ?>
                                            <a class="btn btn-xs btn-info" onclick='lhinst.buttonClicked(<?php echo json_encode($itemButton['content']['payload'])?>,<?php echo $messageId?>,true)'>
                                            <?php endif?>
                                            <?php echo htmlspecialchars($itemButton['content']['name'])?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-xs-3">
                    <?php if ($item['content']['img'] != '' && ($index != 0 || $listCompactStyle == true)) : ?>
                        <img class="pull-right img-responsive" src="<?php echo $item['content']['img']?>" />
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php foreach ($metaMessage['list_quick_replies'] as $index => $item) : ?>
    <div class="list-group-element button-item <?php $listCompactStyle == true ? print ' compact' : print ' large'?>">
        <?php if ($item['type'] == 'url') : ?>
        <a target="_blank" href="<?php echo htmlspecialchars($item['content']['payload'])?>">
        <i class="material-icons">open_in_new</i>
        <?php elseif ($item['type'] == 'updatechat') : ?>
        <a onclick='lhinst.updateChatClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,true)'>
        <?php else : ?>
        <a onclick='lhinst.buttonClicked(<?php echo json_encode($item['content']['payload'])?>,<?php echo $messageId?>,true)'>
        <?php endif?>
        <?php echo htmlspecialchars($item['content']['name'])?></a>
    </div>
    <?php endforeach; ?>
</div>