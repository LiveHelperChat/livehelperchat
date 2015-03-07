<?php include(erLhcoreClassDesign::designtpl('lhuser/menu_tabs_content/personal_canned_messages_tab_pre.tpl.php'));?>
<?php if ($user_menu_tabs_content_personal_canned_messages_tab == true && erLhcoreClassUser::instance()->hasAccessTo('lhuser','personalcannedmsg')) : ?>
<div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_canned') : ?>active<?php endif;?>" id="canned">
     <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/canned_messages.tpl.php'));?>
</div>
<?php endif;?>