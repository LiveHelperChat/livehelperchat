<div class="modal-dialog modal-xl">
    <div class="modal-content">

        <div class="modal-header pt-1 pb-1 ps-2 pe-2" ng-non-bindable>
            <h4 class="modal-title" id="myModalLabel"><span class="material-icons">info_outline</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Change mailbox');?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form action="<?php echo erLhcoreClassDesign::baseurl('mailconv/changemailbox')?>/<?php echo $mail->id?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">
            <div class="modal-body">
                <?php if (isset($errors)) : ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
                <?php endif; ?>

                <?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Mailbox was updated!'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
                    <script>
                        ee.emitEvent('mailChatModified', [<?php echo $mail->id?>]);
                    </script>
                <?php endif; ?>

                    <input class="form-control mb-2 form-control-sm" onkeyup="searchUserTransfer()" id="search-changemailbox" type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Search for a mailbox. First 50 mailbox are shown.');?>">

                    <div class="form-group" id="search-changemailbox-result">
                        <select id="id_new_mailbox_id" name="new_mailbox_id" class="form-control form-control-sm" size="10">
                            <?php foreach (erLhcoreClassModelMailconvMailbox::getList(['limit' => 50,'filter' => ['active' => 1], 'sort' => 'name ASC']) as $mailbox) : ?>
                                <option value="<?php echo $mailbox->id?>"><?php echo htmlspecialchars($mailbox->name),' | ',htmlspecialchars($mailbox->mail) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Update');?></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconv','Close');?></button>
            </div>
        </form>
    </div>
</div>

<script>
function searchUserTransfer() {
    var value = $('#search-changemailbox').val();
    $.getJSON(WWW_DIR_JAVASCRIPT+ 'chat/searchprovider/mailbox/?exclude_disabled=1&q='+escape(value), function(result){
        var resultHTML = '';
        result.items.forEach(function(item){
            var selected = 1 == item.id ? ' selected="selected" ' : '';
            resultHTML += "<option " + selected + " value=\""+item.id+"\">" + $("<div>").text(item.name + (item.mail != "" ? " | " + item.mail : '')).html() + "</option>";
        });
        $('#id_new_mailbox_id').html(resultHTML);
    });
}
</script>