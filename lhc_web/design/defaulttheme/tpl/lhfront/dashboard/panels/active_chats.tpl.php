<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>

    <?php
    $permissionsWidget = [];
    if (erLhcoreClassUser::instance()->hasAccessTo('lhstatistic','statisticdep')){
        $permissionsWidget[] = 'lhstatistic_statisticdep';
    }
    ?>
    <lhc-widget <?php if (isset($customCardNoId)) : ?>no_panel_id="true"<?php endif;?> <?php if (isset($customCardNoCollapse)) : ?>no_collapse="true"<?php endif; ?> additional_sort="active_chats_sort" <?php if (isset($customCardNoDuration)) : ?>no_duration="<?php echo $customCardNoDuration?>"<?php endif; ?> <?php if (isset($customCardTitleClass)) : ?>custom_title_class="<?php echo $customCardTitleClass?>"<?php endif; ?> permissions='<?php echo json_encode($permissionsWidget);?>' <?php if (isset($rightPanelMode)) : ?>right_panel_mode="true"<?php endif; ?> <?php if (isset($hideCardHeader)) : ?>hide_header="true"<?php endif;?> type="active_chats" sort_identifier="active_chats_sort" icon_class="chat-active" type="active_chats" status_id="1" expand_identifier="activec_widget_exp" list_identifier="active-chats" panel_list_identifier="actived-panel-list" optionsPanel={"panelid":"actived","limitid":"limita","userid":"activeu"} www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>"></lhc-widget>
<?php endif; ?>