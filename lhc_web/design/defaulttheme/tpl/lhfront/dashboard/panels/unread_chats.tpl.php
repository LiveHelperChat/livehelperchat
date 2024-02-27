<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>

    <lhc-widget <?php if (isset($customCardNoId)) : ?>no_panel_id="true"<?php endif;?> <?php if (isset($rightPanelMode)) : ?>right_panel_mode="true"<?php endif; ?> <?php if (isset($hideCardHeader)) : ?>hide_header="true"<?php endif;?> icon_class="chat-unread" limit_list_identifier="limitu" type="unread_chats" status_id="1" status_key="hum" expand_identifier="unchats_widget_exp" list_identifier="unread-chats" height_identifier="unreadd_m_h" panel_list_identifier="unreadd-panel-list" optionsPanel='{"panelid":"unreadd","limitid":"limitu"}' www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>"></lhc-widget>

<?php endif; ?>