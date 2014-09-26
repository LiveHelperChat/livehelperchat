var subStatus = '';
<?php if ( erLhcoreClassModelChatConfig::fetch('need_help_tip')->current_value == 1) : ?>
if (this.isOnline == true) {
var lhc_hnh = <?php if (erLhcoreClassModelChatConfig::fetch('need_help_tip_timeout')->current_value > 0) : ?>lh_inst.getPersistentAttribute('lhc_hnh');<?php else : ?>lh_inst.cookieData.lhc_hnh;<?php endif;?>
if (lhc_hnh == null || lhc_hnh == undefined || parseInt(lhc_hnh) < <?php echo time()?>) {
var titleText = (typeof LHCChatOptions.opt.nh_title_text != 'undefined') ? LHCChatOptions.opt.nh_title_text : '<?php if ($theme !== false && $theme->need_help_header !== '') : print htmlspecialchars_decode($theme->need_help_header,ENT_QUOTES); else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Need help?')?><?php endif;?>';
var subTitleText = (typeof LHCChatOptions.opt.nh_sub_title_text != 'undefined') ? LHCChatOptions.opt.nh_sub_title_text : '<?php if ($theme !== false && $theme->need_help_text !== '') : print htmlspecialchars_decode($theme->need_help_text,ENT_QUOTES); else : ?><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Our staff is always ready to help')?><?php endif;?>';
var imageTooltip = (typeof LHCChatOptions.opt.nh_image != 'undefined') ? LHCChatOptions.opt.nh_image : '<?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php if ($theme !== false && $theme->need_help_image_url !== false) : print $theme->need_help_image_url; else : ?><?php echo erLhcoreClassDesign::design('images/general/operator.png');?><?php endif;?>';
subStatus = '<div id="lhc_need_help_container" style="<?php echo $currentPosition['nh_hor_pos']?>">'+
'<span id="lhc_need_help_triangle" style="<?php echo $currentPosition['nh_tr_pos']?>"></span>'+
'<a id="lhc_need_help_close" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" onclick="return lh_inst.lhc_need_help_hide();" href="#">×</a>';
if (imageTooltip !== false) {
subStatus += '<div onclick="return lh_inst.lhc_need_help_click();" id="lhc_need_help_image"><img width="60" height="60" src="' + imageTooltip + '"></div>';
};
subStatus += '<div onclick="return lh_inst.lhc_need_help_click();" id="lhc_need_help_main_title">'+titleText+'</div>'+
'<span onclick="return lh_inst.lhc_need_help_click();" id="lhc_need_help_sub_title">'+subTitleText+'</span>'+
'</div>';};

if (!this.cssNHWasAdded) {
	this.cssNHWasAdded = true;
	var raw_css_need_hl = '#lhc_need_help_container{width:235px;border-radius:20px;background:#<?php $theme !== false ? print $theme->need_help_bcolor : print '92B830' ?>;position:absolute;color:#<?php $theme !== false ? print $theme->need_help_tcolor : print 'ffffff' ?>;padding:10px;border:1px solid #<?php $theme !== false ? print $theme->need_help_border : print 'dbe257' ?>;margin-top:-105px;}#lhc_need_help_container:hover{background-color:#<?php $theme !== false ? print $theme->need_help_hover_bg : print '84A52E' ?>}#lhc_need_help_container:hover #lhc_need_help_triangle{border-top-color:#<?php $theme !== false ? print $theme->need_help_hover_bg : print '84A52E' ?>}'+
	'#lhc_need_help_triangle{width: 0;height: 0;border-left: 20px solid transparent;border-right: 10px solid transparent;border-top: 15px solid #<?php $theme !== false ? print $theme->need_help_bcolor : print '92B830' ?>;position:absolute;bottom:-14px;}'+
	'#lhc_need_help_close{float:right;border-radius:10px;background:#<?php $theme !== false ? print $theme->need_help_close_bg : print '435A05' ?>;padding:0px 6px;color:#FFF;right:10px;font-size:16px;font-weight:bold;text-decoration:none;margin-top:0px;line-height:20px}#lhc_need_help_close:hover{background-color:#<?php $theme !== false ? print $theme->need_help_close_hover_bg : print '74990F' ?>;}'+
	'#lhc_need_help_image{padding-right:10px;float:left;cursor:pointer;}#lhc_need_help_image img{border-radius:30px;border:1px solid #d0d0d0}#lhc_need_help_main_title{font-size:16px;font-weight:bold;cursor:pointer;line-height:1.5}#lhc_need_help_sub_title{cursor:pointer;line-height:1.5}';
	this.addCss(raw_css_need_hl);
};
};

<?php endif;?>