<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','Accept a shared views');
$modalSize = 'xl';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<div class="modal-body">
    <?php if (isset($updated) && $updated == true) : ?>
        <div role="alert" class="alert alert-info alert-dismissible fade show m-3">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','Search was accepted') ?>. <a href="<?php echo erLhcoreClassDesign::baseurl('views/home')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','Go to my views')?></a>
        </div>
        <script>
            ee.emitEvent('views.updateViews', []);
        </script>
    <?php endif; ?>

    <?php if (isset($rejected) && $rejected == true) : ?>
        <div role="alert" class="alert alert-info alert-dismissible fade show m-3">
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','View was rejected') ?>.
        </div>
    <?php endif; ?>

    <?php foreach ($shared_views as $view) : ?>
    <form action="<?php echo erLhcoreClassDesign::baseurl('views/acceptinvites')?>/(view)/<?php echo $view->id?>" method="post" ng-non-bindable target="_blank" onsubmit="return lhinst.submitModalForm($(this))">
        <div class="row">
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
            <div class="col-4 fs13">
                <div><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','Name') ?>:</div>
                <div class="fw-bold pb-2"><?php echo htmlspecialchars($view->name)?></div>
                <div><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','Shared by') ?>:</div>
                <div class="fw-bold">
                    <?php echo htmlspecialchars(($user = erLhcoreClassModelUser::fetch($view->sharer_user_id)) && is_object($user) ? $user->name_official : $view->sharer_user_id)?>
                </div>
            </div>
            <div class="col-8">
                <div class="fs13"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','Description') ?>:</div>
                <?php echo htmlspecialchars($view->description ? $view->description : '-')?>
            </div>
            <div class="col-12 pt-2">
                <div class="btn-group btn-group-toggle" data-bs-toggle="buttons">
                    <input type="hidden" name="ActionView" id="id_ActionView" value="0">
                    <button type="submit" value="AcceptAction" name="AcceptAction" onclick="$('#id_ActionView').val(0)" class="btn btn-xs btn-success"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','Accept a view')?></button>
                    <button type="submit" value="RejectAction" name="RejectAction" onclick="$('#id_ActionView').val(1)" class="btn btn-xs btn-warning"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','Reject a view')?></button>
                </div>
            </div>
        </div>
    </form>
    <?php endforeach; ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>