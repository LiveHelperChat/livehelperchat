<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail actions history')?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

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

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>