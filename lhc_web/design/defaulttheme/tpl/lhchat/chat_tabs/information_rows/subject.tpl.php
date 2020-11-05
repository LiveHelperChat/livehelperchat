<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','setsubject')) : ?>
<tr>
    <td><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Subject')?></td>
    <td>
        <?php $subjectsChat = erLhAbstractModelSubjectChat::getList(array('filter' => array('chat_id' => $chat->id)));
        foreach ($subjectsChat as $subject) : ?><button class="btn btn-xs btn-outline-info"><?php echo htmlspecialchars($subject)?></button>&nbsp;<?php endforeach; ?>
        <button type="button" class="btn btn-xs btn-outline-secondary pb-1 pl-1" onclick="return lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/subject')?>/<?php echo $chat->id?>'})"><i class="material-icons mr-0">&#xE145;</i></button>
    </td>
</tr>
<?php endif; ?>