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
$optionsPanel = array('panelid' => 'operatord', 'limitid' => 'limito', 'disable_product' => true, 'userid' => 'oopu');
?>

<?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/online_operators_panel_multiinclude.tpl.php')); ?>
<lhc-widget mh_widget="100%" right_panel_mode="true" column_2_width="18%" column_1_width="67%" hide_ac_stats="true" hide_ac_op_icon="true" hide_third_column="true" hide_op_avatar="true" no_collapse="true" no_expand="true" hide_filter_options="true" hide_header="true" no_panel_id="true" icon_class="chat-active" data_panel_id="online_operators" permissions='<?php echo json_encode($permissionsWidget);?>' list_identifier="operators" column_1_width="50%" column_2_width="5%" column_3_width="30%" type="online_op" no_link="true" card_icon="account_box" optionsPanel='<?php echo json_encode($optionsPanel)?>' www_dir_flags="<?php echo erLhcoreClassDesign::design('images/flags');?>" expand_identifier="ooperators_widget_exp" sort_identifier = "onop_sort" panel_list_identifier="operatord-panel-list"></lhc-widget>




