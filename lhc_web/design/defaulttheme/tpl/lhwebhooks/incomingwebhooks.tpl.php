<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Incoming webhooks list')?></h1>

<form action="<?php echo erLhcoreClassDesign::baseurl('webhooks/incomingwebhooks')?>" method="get" class="mb-2" autocomplete="off">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'department_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => $input->department_ids,
                    'ajax'           => 'deps',
                    'css_class'      => 'form-control form-control-sm',
                    'display_name'   => 'name',
                    'list_function_params' => array_merge(['sort' => '`name` ASC', 'limit' => 50], erLhcoreClassUserDep::conditionalDepartmentFilter()),
                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
                )); ?>
            </div>
        </div>
        <div class="col-md-8 d-flex align-items-end">
            <div class="form-group">
                <input type="submit" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" name="doSearch">
                <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('webhooks/incomingwebhooks')?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Reset');?></a>
            </div>
        </div>
    </div>
</form>

<script>
$(function() {
    $('.btn-block-department').makeDropdown();
});
</script>

<?php if (isset($imported_rest)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Webhook imported'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($items)) : ?>
    <table class="table" cellpadding="0" cellspacing="0" width="100%" ng-non-bindable>
        <thead>
        <tr>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Name');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Identifier');?></th>
            <th nowrap="nowrap"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Disabled');?></th>
            <th width="1%">&nbsp;</th>
            <th width="1%">&nbsp;</th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td nowrap="nowrap">
                    <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Download')?>" href="?id=<?php echo $item->id?>"><i class="material-icons">cloud_download</i></a>
                    <a href="<?php echo erLhcoreClassDesign::baseurl('webhooks/editincoming')?>/<?php echo $item->id?>"><?php echo htmlspecialchars($item->name)?></a>
                    <?php include(erLhcoreClassDesign::designtpl('lhaudit/modifyinfo.tpl.php')); ?>
                </td>
                <td><?php echo htmlspecialchars($item->identifier)?></td>
                <td nowrap="nowrap"><?php echo htmlspecialchars($item->disabled == 1 ? 'Y' : 'N')?></td>
                <td><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('webhooks/editincoming')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('webhooks/module','Edit');?></a></td>
                <td><a class="btn btn-danger btn-xs csfr-post csfr-required" data-trans="delete_confirm" href="<?php echo erLhcoreClassDesign::baseurl('webhooks/deleteincoming')?>/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Delete');?></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif; ?>

<form action="" method="post" class="form-inline" autocomplete="off" enctype="multipart/form-data">
    <div class="form-group me-2">
        <a href="<?php echo erLhcoreClassDesign::baseurl('webhooks/newincoming')?>" class="btn btn-sm btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','New');?></a>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
    <div class="form-group">
        <input title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','File')?> (json)" accept=".json" type="file" class="form-control-file" name="restfile" value="" />
    </div>
    <input type="submit" name="ImportRestAPI" class="btn btn-secondary btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/import','Import')?>" />
</form>