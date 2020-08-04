<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 pl-2 pr-2">
            <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mail actions history')?></h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">

        <small>
            <?php $messages = array_reverse(erLhcoreClassModelMailconvMessageInternal::getList(array('limit' => 100,'sort' => 'id DESC','filter' => array('chat_id' => $chat->id)))); ?>
            <?php if (!empty($messages)) : ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_obj_list_admin.tpl.php'));?>
            <?php else : ?>
                <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','No history')?></p>
            <?php endif; ?>
        </small>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>