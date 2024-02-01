<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>

    <?php
    $permissionsWidget = [];
    if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','redirectcontact')){
        $permissionsWidget[] = 'lhchat_redirectcontact';
    }
    if (erLhcoreClassUser::instance()->hasAccessTo('lhstatistic','statisticdep')){
        $permissionsWidget[] = 'lhstatistic_statisticdep';
    }
    if (erLhcoreClassUser::instance()->hasAccessTo('lhchat','deletechat')){
        $permissionsWidget[] = 'lhchat_deletechat';
    }
    ?>

    <lhc-widget <?php if (isset($customCardNoId)) : ?>no_panel_id="true"<?php endif;?> <?php if (isset($customCardNoCollapse)) : ?>no_collapse="true"<?php endif; ?> additional_sort="pending_chats_sort" permissions='<?php echo json_encode($permissionsWidget);?>' <?php if (isset($customCardNoDuration)) : ?>no_duration="<?php echo $customCardNoDuration?>"<?php endif; ?> <?php if (isset($customCardTitleClass)) : ?>custom_title_class="<?php echo $customCardTitleClass?>"<?php endif; ?> <?php if (isset($rightPanelMode)) : ?>right_panel_mode="true"<?php endif; ?> <?php if (isset($hideCardHeader)) : ?>hide_header="true"<?php endif;?> type="pending_chats" www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>" default_sort="<?php (int)erLhcoreClassModelChatConfig::fetchCache('reverse_pending')->current_value == 1 ? print "id_asc" : print "id_desc"?>"></lhc-widget>

<?php endif; ?>