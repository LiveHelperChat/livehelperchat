<?php include(erLhcoreClassDesign::designtpl('lhchat/lists_titles/blockedusers.tpl.php'));?>

<?php if (isset($block_saved) && $block_saved == true) : ?>
		<?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Updated'); ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<div class="row" ng-non-bindable>
    <div class="col-6">
        <form class="mb-2" action="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>" >
            <div class="row">
                <div class="col">
                    <input type="text" class="form-control form-control-sm" name="ip" value="<?php echo htmlspecialchars($input->ip)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','IP');?>" />
                </div>
                <div class="col">
                    <input type="text" class="form-control form-control-sm" name="nick" value="<?php echo htmlspecialchars($input->nick)?>" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Nick/Email');?>" />
                </div>
                <div class="col-2">
                    <input type="submit" class="btn btn-sm btn-secondary w-100" name="doSearch" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Search');?>" />
                </div>
            </div>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
        </form>
    </div>
    <div class="col-6">
        <form class="mb-2" action="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>"  method="post">
            <div class="row">
                <div class="col">
                    <input type="text" class="form-control form-control-sm" name="IPToBlock" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','IP/E-mail');?>" />
                </div>
                <div class="col-2">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <input type="submit" class="btn btn-sm btn-warning w-100" name="AddBlock" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Block IP');?>" />
                        <input type="submit" class="btn btn-sm btn-warning w-100" name="AddBlockEmail" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Block e-mail');?>" />
                    </div>
                </div>
            </div>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>
        </form>
    </div>
</div>





<?php if (!empty($items)) : ?>
<table class="table" cellpadding="0" cellspacing="0" ng-non-bindable>
<thead>
<tr>
    <th width="1%">ID</th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Block type');?>, <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','IP');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Department');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Nick/E-mail');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Expires in');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Block date');?></th>
    <th width="20%"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','User who blocked');?></th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($items as $item) : ?>
    <tr>
        <td><?php echo $item->id?></td>
        <td title="<?php echo $item->btype?>">
            <?php if (in_array($item->btype, [erLhcoreClassModelChatBlockedUser::BLOCK_IP,erLhcoreClassModelChatBlockedUser::BLOCK_ALL_IP_NICK,erLhcoreClassModelChatBlockedUser::BLOCK_ALL_IP_NICK_DEP])) : ?>
                <span class="badge badge-secondary">IP</span>
            <?php endif; ?>

            <?php if (in_array($item->btype, [erLhcoreClassModelChatBlockedUser::BLOCK_NICK,erLhcoreClassModelChatBlockedUser::BLOCK_ALL_IP_NICK])) : ?>
                <span class="badge badge-secondary">Nick</span>
            <?php endif; ?>

            <?php if (in_array($item->btype, [erLhcoreClassModelChatBlockedUser::BLOCK_NICK_DEP,erLhcoreClassModelChatBlockedUser::BLOCK_ALL_IP_NICK_DEP])) : ?>
                <span class="badge badge-secondary">Nick &amp; Department</span>
            <?php endif; ?>

            <?php if (in_array($item->btype, [erLhcoreClassModelChatBlockedUser::BLOCK_EMAIL])) : ?>
                <span class="badge badge-secondary">E-mail</span>
            <?php endif; ?>

            <?php if ($item->chat_id > 0) : ?><a class="material-icons" title="<?php echo htmlspecialchars($item->chat_id)?>" onclick="lhc.previewChat(<?php echo $item->chat_id?>)">info_outline</a><?php endif; ?><?php echo htmlspecialchars($item->ip)?>
        </td>
        <td><?php echo htmlspecialchars($item->department)?></td>
        <td><?php echo htmlspecialchars($item->nick)?></td>
        <td><?php echo htmlspecialchars($item->expires_front)?> [<?php echo $item->block_duration?>]</td>
        <td><?php echo htmlspecialchars($item->datets_front)?></td>
        <td><?php echo htmlspecialchars($item->user)?></td>
        <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('chat/blockedusers')?>/(remove_block)/<?php echo $item->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Remove block');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php else : ?>
<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/blockedusers','Empty...');?></p>
<?php endif; ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>