<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Block sender')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
    <div id="block-bloc-<?php echo $mail->id?>"></div>
    <form id="block-form">
        <?php foreach (erLhcoreClassModelChatBlockedUser::isBlocked(array('return_block' => true, 'email_conv' => $mail->from_address)) as $blockItem) : ?>
            <div class="py-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Bocked by')?>&nbsp;<a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>?id=<?php echo $blockItem->id?>"><span class="material-icons">open_in_new</span><b><?php echo $blockItem->id?></b></a>&nbsp;
                <span class="badge bg-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Sender E-mail');?></span>
            </div>
        <?php endforeach; ?>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Expires')?></label>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/blockuser/blockoptions_expire.tpl.php'));?>
        </div>
        <button type="button" onclick="blockByChat()" class="btn btn-sm btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Block')?></button>
    </form>
    <script>
        function blockByChat(){
            $.postJSON(WWW_DIR_JAVASCRIPT + 'mailconv/blocksender/<?php echo $mail->id?>', $('#block-form').serialize(), function(data){
                $('#block-bloc-<?php echo $mail->id?>').html(data.result);
                if (!data.error) {
                    setTimeout(function (){
                        $('#myModal').modal('hide');
                        ee.emitEvent('mailChatModified',[<?php echo $mail->id?>]);
                    },2000);
                }
            });
        }
    </script>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>