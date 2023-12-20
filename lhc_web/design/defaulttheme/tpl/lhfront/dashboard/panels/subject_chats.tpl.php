<?php if ($currentUser->hasAccessTo('lhchat','use')) : ?>


    <lhc-widget <?php if (isset($customCardNoId)) : ?>no_panel_id="true"<?php endif;?> icon_class="chat-active" list_identifier="subject" type="subject_chats" no_link="true" column_1_Width="27%" column_2_Width="18%" column_3_Width="10%" card_icon="label" <?php if ($currentUser->hasAccessTo('lhchat','subject_chats_options')) : ?>custom_settings_url="chat/subjectwidget"<?php endif; ?> optionsPanel='<?php echo json_encode(array('panelid' => 'subjectd', 'limitid' => 'limits', 'userid' => 'subjectu'))?>' www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>" expand_identifier="subjectc_widget_exp" panel_list_identifier="subjectd-panel-list"></lhc-widget>

<?php endif; ?>