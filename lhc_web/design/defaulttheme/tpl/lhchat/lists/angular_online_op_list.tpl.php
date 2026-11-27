<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/angular_online_op_list_tab_pre.tpl.php')); ?>
<?php if ($chat_lists_online_operators_enabled == true) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhfront/dashboard/panels/online_operators.tpl.php')); ?>
<?php endif;?>