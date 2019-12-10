<?php include(erLhcoreClassDesign::designtpl('lhuser/menu_tabs/personal_auto_responder_tab_pre.tpl.php'));?>
<?php if ($user_menu_tabs_personal_auto_responder_tab == true && erLhcoreClassUser::instance()->hasAccessTo('lhuser','personalautoresponder')) : ?>
    <div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_autoresponder') : ?>active<?php endif;?>" id="autoresponder">
        <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/personal_auto_responder.tpl.php'));?>
    </div>
<?php endif;?>
