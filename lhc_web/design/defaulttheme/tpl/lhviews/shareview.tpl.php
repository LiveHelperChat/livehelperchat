<?php
$modalHeaderClass = 'pt-1 pb-1 ps-2 pe-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','Share a view');
$modalSize = 'xl';
$modalBodyClass = 'p-1';
$appendPrintExportURL = '';
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<form action="<?php echo erLhcoreClassDesign::baseurl('views/shareview')?>/<?php echo $view->id?>" method="post" ng-non-bindable target="_blank" onsubmit="return lhinst.submitModalForm($(this))">

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="modal-body">
        <?php if (isset($updated) && $updated == true) : ?>
            <div role="alert" class="alert alert-info alert-dismissible fade show m-3">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','View was shared') ?>.
            </div>
        <?php endif; ?>
        <?php if (isset($errors)) : ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <?php endif; ?>
        <div class="row">
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
            <div class="col-12">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','Set name for a share') ?>*</label>
                    <input type="text" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($view->name);?>">
                </div>
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','Set custom description') ?></label>
                    <textarea class="form-control form-control-sm"><?php echo htmlspecialchars($view->description);?></textarea>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','Share with') ?>*</label>
                    <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                        'input_name'     => 'user_ids[]',
                        'optional_field' =>  erTranslationClassLhTranslation::getInstance()->getTranslation('views/view','Select user'),
                        'selected_id'    => $user_id,
                        'no_selector'    => true,
                        'css_class'      => 'form-control',
                        'display_name'   => 'name_official',
                        'ajax'           => 'users',
                        'list_function_params' => array_merge(erLhcoreClassGroupUser::getConditionalUserFilter(),array('sort' => '`name` ASC','limit' => 50)),
                        'list_function'  => 'erLhcoreClassModelUser::getUserList',
                    )); ?>
                </div>
                <script>$('.btn-block-department').makeDropdown();</script>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary btn-sm pull-left"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Share')?></button>
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Close')?></button>
    </div>
</form>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>