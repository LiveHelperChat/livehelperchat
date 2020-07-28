<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/cbscheduler','Edit');?></h1>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('mailconv/editmailbox')?>/<?php echo $item->id?>" method="post">

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li role="presentation" class="nav-item"><a href="#settings" class="nav-link<?php if ($tab == '') : ?> active<?php endif;?>" aria-controls="settings" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Settings');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'tab_mailbox') : ?> active<?php endif;?>" href="#mailbox" aria-controls="mailbox" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Mailbox');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'tab_utilities') : ?> active<?php endif;?>" href="#utilities" aria-controls="utilities" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Utilities');?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="settings">
            <?php include(erLhcoreClassDesign::designtpl('lhmailconv/parts/form.tpl.php'));?>

            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" class="btn btn-secondary" name="Save_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
                <input type="submit" class="btn btn-secondary" name="Update_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
                <input type="submit" class="btn btn-secondary" name="Cancel_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_mailbox') : ?>active<?php endif;?>" id="mailbox">
            <a class="btn btn-secondary btn-sm" title="Mailboxes" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/editmailbox')?>/<?php echo $item->id?>/(action)/mailbox?r=<?php echo time()?>#!#mailbox" ><i class="material-icons">sync</i>Get mailbox to sync</a>

            <hr>
            <h5>Choose what mailbox you want to sync</h5>

            <?php foreach ($item->mailbox_sync_array as $mailbox) : ?>
            <div class="form-group">
                <label><input type="checkbox" value="<?php echo htmlspecialchars($mailbox['path'])?>" <?php if ($mailbox['sync'] == true) : ?>checked="checked"<?php endif; ?> name="Mailbox[]"> <?php echo htmlspecialchars($mailbox['path'])?></label>
            </div>
            <?php endforeach; ?>

            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" class="btn btn-secondary" name="Save_mailbox" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
            </div>

        </div>

        <div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_utilities') : ?>active<?php endif;?>" id="utilities">
            <a class="btn btn-secondary btn-sm" title="Sync messages" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/editmailbox')?>/<?php echo $item->id?>/(action)/sync?r=<?php echo time()?>#!#utilities" ><i class="material-icons">sync</i>Check for a new messages</a> <span class="mt-4">Last sync - <?php echo $item->last_sync_time_ago?> ago.</span>

            <h5 class="mt-4">Sync log</h5>
            <pre><?php echo htmlspecialchars(print_r($item->last_sync_log_array,true))?></pre>

        </div>
    </div>

</form>