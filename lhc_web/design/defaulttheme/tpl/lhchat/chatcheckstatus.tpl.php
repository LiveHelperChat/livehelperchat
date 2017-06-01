<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/is_online_help.tpl.php')); ?>
<?php if ($status == true && $isOnlineHelp == false) : ?>
lh_inst.isOnline = false;
lh_inst.showStatusWidget();
lh_inst.stopCheckNewMessage();

<?php if ($isproactive == true) : ?>
lh_inst.hide();
<?php endif;?>

<?php if ($hide_offline == true) : ?>
lh_inst.toggleStatusWidget(true);
<?php endif;?>

<?php elseif ($status == false && $isOnlineHelp == true) : ?>
lh_inst.isOnline = true;
lh_inst.showStatusWidget();
if (!lh_inst.cookieData.hash && lh_inst.disabledGeo == false){
	if (lh_inst.checkOperatorMessage == true) {
		lh_inst.startNewMessageCheck();
	} else {
		lh_inst.startNewMessageCheckSingle();
	}
}
<?php endif;?>