<?php if ($message_body === false) : ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 pl-2 pr-2">
            <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span>&nbsp;Preview message</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
    <small>
<?php endif; ?>
            <div id="messagesBlockWrap">
                <div id="messagesBlock">
                    <div class="message-row message-admin operator-changes"><span class="usr-tit op-tit"><i class="material-icons chat-operators mi-fs15 mr-0">account_box</i>Operator</span>
                        <?php $msgBody = $msg; ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_body.tpl.php'));?>
                    </div>
                </div>
            </div>

<?php if ($message_body === false) : ?>
</small>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>
<?php endif; ?>