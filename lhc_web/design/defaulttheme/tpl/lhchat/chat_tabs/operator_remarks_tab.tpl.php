<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_remarks_tab_pre.tpl.php'));?>
<?php if ($operator_remarks_tab_enabled == true) : ?>
<li role="presentation" class="nav-item" >
    <a class="nav-link <?php if ($chatTabsOrderDefault == 'operator_remarks_tab') print ' active';?>" href="#main-user-info-remarks-<?php echo $chat->id?>" aria-controls="main-user-info-remarks-<?php echo $chat->id?>" role="tab" data-bs-toggle="tab" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Remarks')?>">
        <span <?php if ($chat->remarks != '' || (($online_user = $chat->online_user) !== false && $online_user->notes != '')) : ?>class="badge bg-warning"<?php endif;?>><i class="material-icons me-0">mode_edit</i><?php if ($chat->remarks != '' || (($online_user = $chat->online_user) !== false && $online_user->notes != '')) : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','has notes')?><?php endif;?></span>
    </a>
</li>
<?php endif;?>