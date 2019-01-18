<?php include(erLhcoreClassDesign::designtpl('lhchat/chat_tabs/operator_remarks_tab_pre.tpl.php'));?>
<?php if ($operator_remarks_tab_enabled == true) : ?>
<li role="presentation"<?php if ($chatTabsOrderDefault == 'operator_remarks_tab') print ' class="active"';?>><a href="#main-user-info-remarks-<?php echo $chat->id?>" aria-controls="main-user-info-remarks-<?php echo $chat->id?>" role="tab" data-toggle="tab" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Remarks')?>"><i class="material-icons mr-0">mode_edit</i></a></li>
<?php endif;?>