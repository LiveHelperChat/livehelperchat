<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail actions history')?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

        <ul class="nav nav-pills" role="tablist">
            <li role="presentation" class="nav-item"><a class="active nav-link" href="#mainmailmodify" aria-controls="mainmailmodify" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Actions');?></a></li>

            <?php if ($chat->is_archive === false && erLhcoreClassUser::instance()->hasAccessTo('lhchat','chatdebug')) : ?>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#chatdebug" aria-controls="chatdebug" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Debug');?></a></li>
            <?php endif; ?>

            <?php if ($chat->is_archive === false && erLhcoreClassUser::instance()->hasAccessTo('lhaudit','see_op_actions')) : ?>
                <li role="presentation" class="nav-item"><a class="nav-link" href="#opactions" aria-controls="opactions" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Actions');?></a></li>
            <?php endif; ?>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="mainmailmodify">
                <small>
                    <?php
                    $messages = $chat->is_archive === false ? array_reverse(erLhcoreClassModelMailconvMessageInternal::getList(array('limit' => 100,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id)))) :  array_reverse(LiveHelperChat\Models\mailConv\Archive\MessageInternal::getList(array('limit' => 100,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id))));
                    $paramsMessageRenderExecution = ['extend_date' => true];
                    ?>
                    <?php if (!empty($messages)) : ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_obj_list_admin.tpl.php'));?>
                    <?php else : ?>
                        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No history')?></p>
                    <?php endif; ?>
                </small>
            </div>
            <?php if ($chat->is_archive === false && erLhcoreClassUser::instance()->hasAccessTo('lhchat','chatdebug')) : ?>
            <div role="tabpanel" class="tab-pane mx550" id="chatdebug">
                <?php
                $state = $chat->getState();
                $state['body'] = 'REMOVED';
                ?>
                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Conversation');?></h6>
                <pre class="fs11"><?php echo htmlspecialchars(json_encode($state,JSON_PRETTY_PRINT)); ?></pre>

                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/modifychat','Messages');?></h6>
                <?php foreach (erLhcoreClassModelMailconvMessage::getList(array('ignore_fields' => ['body','alt_body'],'filter' => array('conversation_id' => $chat->id))) as $message) : ?>
                    <pre class="fs11"><?php echo htmlspecialchars(json_encode($message->getState(),JSON_PRETTY_PRINT)); ?></pre>
                <?php endforeach;?>
            </div>
            <?php endif; ?>
            <?php if ($chat->is_archive === false && erLhcoreClassUser::instance()->hasAccessTo('lhaudit','see_op_actions')) : ?>
                <div role="tabpanel" class="tab-pane mx550" id="opactions">
                    <?php $opActionsParams = ['scope' => 'mail', 'object_id' => $chat->id]; ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhaudit/op_actions_object.tpl.php'));?>
                </div>
            <?php endif; ?>
        </div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>