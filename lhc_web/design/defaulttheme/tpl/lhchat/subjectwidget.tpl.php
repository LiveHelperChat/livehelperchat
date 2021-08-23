<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">
                <span class="material-icons">info_outline</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Filter chats by subject')?>
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">

            <?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/dashboardwidgets','Settings updated'); ?>
                <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
                <script>
                    setTimeout(function(){
                        location.reload();
                    },250);
                </script>
            <?php endif; ?>

            <form action="<?php echo erLhcoreClassDesign::baseurl('chat/subjectwidget')?>" method="post" onsubmit="return lhinst.submitModalForm($(this))">

                <div class="row" style="max-height: 500px; overflow-y: auto">
                <?php foreach (erLhAbstractModelSubject::getList(array('sort' => 'name ASC','limit' => false)) as $item) : ?>
                    <div class="col-6">
                        <label><input name="subject_id[]" <?php if (in_array($item->id,$subject_id)) : ?>checked="checked"<?php endif; ?> type="checkbox" value="<?php echo $item->id?>"> <?php echo htmlspecialchars($item->name)?></label>
                    </div>
                <?php endforeach; ?>
                </div>

                <input type="hidden" name="Update_action" value="on">

                <input type="submit" class="btn btn-secondary btn-sm" name="Update_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update')?>">
            </form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>