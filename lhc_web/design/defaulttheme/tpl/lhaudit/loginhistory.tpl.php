<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/list','Login history')?></h1>

<form ng-non-bindable action="<?php echo $input->form_action?>" method="get" class="pb-2" name="SearchFormRight" autocomplete="off">
    <input type="hidden" name="doSearch" value="1">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User ID');?></label>
                <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo htmlspecialchars($input->user_id)?>" />
            </div>
        </div>
    </div>
    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" name="doSearch" class="btn btn-sm btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
    </div>
</form>

<?php if (isset($items)) : ?>
    <table class="table table-sm" ng-non-bindable cellpadding="0" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','ID');?></th>
            <th width="1%" nowrap=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','User ID');?></th>
            <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Message');?></th>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','IP');?></th>
            <th width="1%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/userlist','Date');?></th>
        </tr>
        </thead>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>
                    <?php echo $item->id?>
                </td>
                <td>
                    <?php echo $item->user_id?>
                </td>
                <td title="<?php echo $item->type?>">
                    <?php if ($item->status == erLhcoreClassModelUserLogin::STATUS_COMPLETED) : ?>
                        <span class="material-icons text-success">done</span>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($item->msg)?>
                </td>
                <td>
                    <?php echo $item->ip?>
                </td>
                <td nowrap>
                    <?php echo $item->ctime_front?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

    <?php if (isset($pages)) : ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
    <?php endif;?>

<?php endif; ?>

