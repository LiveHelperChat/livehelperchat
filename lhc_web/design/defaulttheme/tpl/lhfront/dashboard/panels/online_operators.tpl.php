<?php
$permissionsWidget = [];
if (erLhcoreClassUser::instance()->hasAccessTo('lhstatistic','userstats')){
    $permissionsWidget[] = 'lhstatistic_userstats';
}
if (erLhcoreClassUser::instance()->hasAccessTo('lhuser', 'setopstatus')){
    $permissionsWidget[] = 'lhuser_setopstatus';
}
if (erLhcoreClassUser::instance()->hasAccessTo('lhgroupchat', 'use')){
    $permissionsWidget[] = 'lhgroupchat_use';
}
$optionsPanel = array(
    'panelid' => 'operatord',
    'limitid' => 'limito',
    'disable_product' => true,
    'userid' => 'oopu',
    'custom_filters' => [
            [
                    'field' => 'on_opf',
                    'type'  => 'checkbox',
                    'label' => htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Chat ready')),
                    'title' => htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/syncadmininterface','Show only operators that are online')),
                    'icon' => 'flash_on',
            ]
    ]
);
?>

<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/online_operators_panel_multiinclude.tpl.php')); ?>
<lhc-widget <?php if (isset($customCardNoId)) : ?>no_panel_id="true"<?php endif;?> icon_class="chat-active" data_panel_id="online_operators" permissions='<?php echo json_encode($permissionsWidget);?>' list_identifier="operators" column_1_width="36%" column_2_width="5%" column_3_width="20%" type="online_op" no_link="true" card_icon="account_box" optionsPanel='<?php echo json_encode($optionsPanel)?>' www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>" expand_identifier="ooperators_widget_exp" sort_identifier = "onop_sort" panel_list_identifier="operatord-panel-list"></lhc-widget>

