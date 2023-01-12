<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/chat_actions','Chat actions');?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body mx550">

        <table class="table table-sm table-hover" ng-non-bindable>
            <tr>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/chat_actions','Action');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/chat_actions','Body');?></th>
                <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/chat_actions','Time');?></th>
            </tr>
            <?php foreach (erLhcoreClassModelChatAction::getList(['sort' => 'id ASC', 'limit' => false,'filter' => ['chat_id' => $chat->id]]) as $item) : ?>
            <tr>
                <td>
                    <?php echo htmlspecialchars($item->action)?>
                </td>
                <td>
                    <?php if ($item->body_array !== null) : ?>
                        <?php echo htmlspecialchars(json_encode($item->body_array, JSON_PRETTY_PRINT))?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($item->created_at_front)?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>