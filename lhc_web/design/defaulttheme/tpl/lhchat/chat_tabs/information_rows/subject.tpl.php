<tr>
    <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Subject')?></td>
    <td>
        <?php $subjectsChat = erLhAbstractModelSubjectChat::getList(array('filter' => array('chat_id' => $chat->id)));
        foreach ($subjectsChat as $subject) : ?><a class="btn btn-xs btn-info"><?php echo htmlspecialchars($subject)?></a>&nbsp;<?php endforeach; ?>
        <a class="btn btn-xs btn-success" onclick="return lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/subject')?>/<?php echo $chat->id?>'})"><i class="material-icons mr-0">&#xE145;</i></a>
    </td>
</tr>
