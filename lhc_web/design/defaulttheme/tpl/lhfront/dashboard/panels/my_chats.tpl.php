<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>

    <?php
    $optinsPanel = array('panelid' => 'mcd','limitid' => 'limitmc');
    if (!$currentUser->hasAccessTo('lhchat','my_chats_filter')) {
        $optinsPanel['hide_department_filter'] = true;
        $optinsPanel['limits_width'] = 12;
    }
    ?>

    <lhc-widget <?php if (isset($customCardNoId)) : ?>no_panel_id="true"<?php endif;?> <?php if (isset($customCardNoCollapse)) : ?>no_collapse="true"<?php endif; ?>  <?php if (isset($customCardTitleClass)) : ?>custom_title_class="<?php echo $customCardTitleClass?>"<?php endif; ?> <?php if (isset($customCardNoDuration)) : ?>no_duration="<?php echo $customCardNoDuration?>"<?php endif; ?> column_2_width="25%" card_icon="account_box" <?php if (isset($rightPanelMode)) : ?>right_panel_mode="true"<?php endif; ?> <?php if (isset($hideCardHeader)) : ?>hide_header="true"<?php endif;?> icon_class="chat-active" list_identifier="my-chats" type="my_chats" optionsPanel='<?php echo json_encode($optinsPanel)?>' www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>" expand_identifier="my_chats_widget_exp" status_id="<?php echo erLhcoreClassUser::instance()->getUserID()?>" status_key="user_id" panel_list_identifier="mcd-panel-list"></lhc-widget>
<?php endif; ?>