<div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header pt-1 pb-1 ps-2 pe-2">
            <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/chat_actions','Messages preview');?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body mx550">
            <iframe id="lhc_iframe" class="border rounded-1" allowtransparency="true" scrolling="no" frameborder="0" src="<?php echo erLhcoreClassDesign::baseurl('chat/demo')?>/(debug)/true/(hash)/<?php echo $chat->hash;?>/(id)/<?php echo $chat->id;?>/(department)/<?php echo $chat->department->alias != '' ? htmlspecialchars($chat->department->alias) : $chat->dep_id ; ?>/(leaveamessage)/true<?php echo $chat->theme_id > 0 ? '/(theme)/'.($chat->theme->alias != '' ? htmlspecialchars($chat->theme->alias) : $chat->theme_id) : ''?>" width="320" height="500" style="width: 100%; height: 500px;"></iframe>
        </div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>