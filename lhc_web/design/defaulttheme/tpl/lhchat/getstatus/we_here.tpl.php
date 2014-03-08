var subStatus = '';
<?php if ( $isOnlineHelp == true && erLhcoreClassModelChatConfig::fetch('need_help_tip')->current_value == 1) : ?>
if (!lh_inst.cookieData.hnh) {
var titleText = (typeof LHCChatOptions.opt.nh_title_text != 'undefined') ? LHCChatOptions.opt.nh_title_text : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Need help?')?>';
var subTitleText = (typeof LHCChatOptions.opt.nh_sub_title_text != 'undefined') ? LHCChatOptions.opt.nh_sub_title_text : '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Our staff is always ready to help')?>';
var imageTooltip = (typeof LHCChatOptions.opt.nh_image != 'undefined') ? LHCChatOptions.opt.nh_image : '<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php echo erLhcoreClassDesign::design('images/general/operator.png');?>';
subStatus = '<div id="lhc_need_help_container" style="width:235px;<?php echo $currentPosition['nh_hor_pos']?>border-radius:20px;background:#92B830;position:absolute;color:#Fff;padding:10px;border:1px solid #dbe257;margin-top:-105px;">'+
'<span style="width: 0;height: 0;border-left: 20px solid transparent;border-right: 10px solid transparent;border-top: 15px solid #92B830;position:absolute;<?php echo $currentPosition['nh_tr_pos']?>;bottom:-14px;"></span>'+
'<a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" onclick="return lh_inst.lhc_need_help_hide();" style="float:right;border-radius:10px;background:#435A05;padding:0px 6px;color:#FFF;right:10px;font-size:16px;font-weight:bold;text-decoration:none;margin-top:0px;" href="#">×</a>';
if (imageTooltip !== false) {
subStatus += '<div onclick="return lh_inst.lhc_need_help_click();" style="padding-right:10px;float:left;cursor:pointer;"><img style="border-radius:30px;border:1px solid #d0d0d0" width="60" height="60" src="' + imageTooltip + '"></div>';
};
subStatus += '<div onclick="return lh_inst.lhc_need_help_click();" style="font-size:16px;font-weight:bold;cursor:pointer;">'+titleText+'</div>'+
'<span onclick="return lh_inst.lhc_need_help_click();" style="cursor:pointer;">'+subTitleText+'</span>'+
'</div>';};
<?php endif;?>