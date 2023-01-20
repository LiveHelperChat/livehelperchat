<?php if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','setsubject')) : ?>
<tr>
    <td colspan="2">

        <h6 class="fw-bold"><i class="material-icons">description</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/subject','Subject')?>
            <button type="button" class="btn btn-xs btn-link text-muted pb-1 ps-1" onclick="return lhc.revealModal({'url':'<?php echo erLhcoreClassDesign::baseurl('chat/subject')?>/<?php echo $chat->id?>'})"><i class="material-icons me-0">&#xE145;</i></button>
        </h6>

        <?php $subjectsChat = erLhAbstractModelSubjectChat::getList(array('filter' => array('chat_id' => $chat->id)));
        foreach ($subjectsChat as $subject) : ?><button class="btn btn-xs btn-outline-info"><?php echo htmlspecialchars($subject)?></button>&nbsp;<?php endforeach; ?>

        <?php foreach($chat->aicons as $aicon) : ?>
            <span class="material-icons" title="<?php print isset($aicon['t']) ? htmlspecialchars($aicon['t']) : htmlspecialchars($aicon['i'])?>" <?php if (isset($aicon['c']) && $aicon['c'] != '') : ?>style="color:<?php echo htmlspecialchars($aicon['c'])?>"<?php endif; ?> ><?php echo htmlspecialchars(is_array($aicon) && isset($aicon['i']) ? $aicon['i'] : $aicon)?></span>
        <?php endforeach; ?>

    </td>
</tr>
<?php endif; ?>