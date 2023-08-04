<?php $subMessages = erLhcoreClassBBCode::makeSubmessages($msgBody, isset($paramsMessageRender) ? $paramsMessageRender : array()); ?>
<?php foreach ($subMessages as $subMessage) : ?>
    <?php (in_array('nlt',$subMessage['flags'])) ? print '<br />' : ''; ?>
    <div class="msg-body<?php (in_array('img',$subMessage['flags']))? print ' msg-body-media' : ''?><?php (in_array('emoji',$subMessage['flags']))? print ' msg-body-emoji' : ''?><?php if (isset($paramsMessageRender['msg_body_class']) && !empty($paramsMessageRender['msg_body_class'])) : ?><?php echo ' '.$paramsMessageRender['msg_body_class']?><?php endif; ?>">
        <?php if (isset($paramsMessageRender['sender']) && $paramsMessageRender['sender'] == 0 && !(isset($visitorRender) && $visitorRender == true)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/reaction_to_visitor.tpl.php'));?>
        <?php endif; ?>

        <?php if ((!isset($visitorRender) || $visitorRender !== true) && isset($msg) && isset($msg['del_st']) && is_array($msg) && ($msg['user_id'] > 0 || $msg['user_id'] == -2)) : // Render status only for admin messages and bot?>
            <?php if ($msg['del_st'] == erLhcoreClassModelmsg::STATUS_PENDING) : ?>
                <span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','Scheduled for sent!')?>" class="material-icons text-warning msg-del-st-<?php echo erLhcoreClassModelmsg::STATUS_PENDING;?>">radio_button_unchecked</span>
            <?php elseif ($msg['del_st'] == erLhcoreClassModelmsg::STATUS_SENT) : ?>
                <span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','Have been sent!')?>" class="material-icons text-success msg-del-st-<?php echo erLhcoreClassModelmsg::STATUS_SENT;?>">done</span>
            <?php elseif ($msg['del_st'] == erLhcoreClassModelmsg::STATUS_DELIVERED) : ?>
                <span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','Delivered')?>" class="material-icons text-success msg-del-st-<?php echo erLhcoreClassModelmsg::STATUS_DELIVERED;?>">done_all</span>
            <?php /*elseif ($msg['del_st'] == erLhcoreClassModelmsg::STATUS_READ) : ?>
                <span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','Have been seen!')?>" class="material-icons text-success msg-del-st-<?php echo erLhcoreClassModelmsg::STATUS_READ;?>">how_to_reg</span>
            <?php */ elseif ($msg['del_st'] == erLhcoreClassModelmsg::STATUS_REJECTED) : ?>
                <span title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmin','Rejected or failed!')?>" class="material-icons text-danger msg-del-st-<?php echo erLhcoreClassModelmsg::STATUS_REJECTED;?>">unpublished</span>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ((!isset($visitorRender) || $visitorRender !== true) &&
            (isset($metaMessageData['content']['accept_action']) ||
            isset($metaMessageData['content']['transfer_action_user']) ||
            isset($metaMessageData['content']['transfer_action_dep']) ||
            isset($metaMessageData['content']['change_owner_action']) ||
            isset($metaMessageData['content']['change_dep_action']))) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render_msg_body_admin_pre_msg.tpl.php'));?>
        <?php endif; ?>


        <?php echo $subMessage['body']?>
        <?php if (isset($visitorRender) && $visitorRender == true && isset($metaMessageData)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render_msg_body.tpl.php'));?>
        <?php elseif (isset($metaMessageData)): ?>
            <?php include(erLhcoreClassDesign::designtpl('lhgenericbot/message/meta_render_msg_body_admin.tpl.php'));?>
        <?php endif; ?>
    </div>
    <?php (in_array('nl',$subMessage['flags'])) ? print '<br />' : ''; ?>
<?php endforeach; ?>