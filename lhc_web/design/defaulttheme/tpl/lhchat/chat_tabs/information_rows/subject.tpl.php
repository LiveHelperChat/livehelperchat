<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','setsubject')) : ?>
<tr>
    <td colspan="2">

        <h6 class="fw-bold"><i class="material-icons">description</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Subject')?>
            <?php if (!($chat instanceof erLhcoreClassModelChatArchive)) : ?>
            <button type="button" class="btn btn-xs btn-link text-muted pb-1 ps-1" onclick="return lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/subject')?>/<?php echo $chat->id?>'})"><i class="material-icons me-0">&#xE145;</i></button>
            <?php endif;?>
        </h6>

        <?php

        if ($chat instanceof erLhcoreClassModelChatArchive) {
            $subjectsChat = erLhAbstractModelChatArchiveSubject::getList(array('filter' => array('chat_id' => $chat->id)));
        } else {
            $subjectsChat = erLhAbstractModelSubjectChat::getList(array('filter' => array('chat_id' => $chat->id)));
        }

        foreach ($subjectsChat as $subject) : ?><span class="badge bg-info fs12" <?php if (is_object($subject->subject) && $subject->subject->color != '') : ?>style="background-color:#<?php echo htmlspecialchars($subject->subject->color)?>!important;" <?php endif;?> ><?php echo htmlspecialchars($subject)?></span>&nbsp;<?php endforeach; ?>

        <?php foreach($chat->aicons as $aicon) : ?>
            <?php if (isset($aicon['i']) && strpos($aicon['i'],'/') !== false) : ?>
                <img class="me-1" title="<?php isset($aicon['t']) ? print htmlspecialchars($aicon['t']) : htmlspecialchars($aicon['i'])?>" src="<?php echo $aicon['i'];?>" />
            <?php else : ?>
                <i class="material-icons" style="color: <?php isset($aicon['c']) ? print htmlspecialchars($aicon['c']) : print '#6c757d'?>" title="<?php isset($aicon['t']) ? print htmlspecialchars($aicon['t']) : htmlspecialchars($aicon['i'])?>"><?php isset($aicon['i']) ? print htmlspecialchars($aicon['i']) : htmlspecialchars($aicon)?></i>
            <?php endif; ?>
        <?php endforeach; ?>

    </td>
</tr>
<?php endif; ?>