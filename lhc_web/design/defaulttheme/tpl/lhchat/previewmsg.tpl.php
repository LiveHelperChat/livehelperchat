
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span>&nbsp;Preview message</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
    <small>
    <div id="messagesBlockWrap">
        <div>
                <?php
                $messages = [$msg];
                $current_user_id = erLhcoreClassUser::instance()->getUserID();
                $paramsMessageRenderOverride['show_edit_history'] = true;
                ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/syncadmin.tpl.php'));?>
        </div>
    </div>
</small>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>
