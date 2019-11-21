var subStatus = '';
<?php if (($theme !== false && $theme->show_need_help == 1) || ($theme === false && erLhcoreClassModelChatConfig::fetch('need_help_tip')->current_value == 1)) : ?>
if (this.isOnline == true) {
<?php $needHelpTimeout = $theme !== false ? $theme->show_need_help_timeout : erLhcoreClassModelChatConfig::fetch('need_help_tip_timeout')->current_value; ?>
var lhc_hnh = <?php if ($needHelpTimeout > 0) : ?>lh_inst.getPersistentAttribute('lhc_hnh');<?php else : ?>lh_inst.cookieData.lhc_hnh;<?php endif;?>
if (lhc_hnh == null || lhc_hnh == undefined || parseInt(lhc_hnh) < <?php echo time()?>) {

<?php $nh_hor_pos = ($theme !== false && $theme->modern_look == 0) ? 'nh_hor_pos' : 'nh_hor_pos_modern'; ?>
<?php include(erLhcoreClassDesign::designtpl('lhchat/getstatus/we_here_substatus.tpl.php')); ?>	

if (!this.cssNHWasAdded) {
	this.cssNHWasAdded = true;

    <?php if ($theme !== false && $theme->modern_look == 0) : ?>
        var raw_css_need_hl = '#<?php echo $chatCSSPrefix?>_need_help_container{<?php if ($theme !== false && $theme->show_need_help_delay > 0) : ?>display:none;<?php endif;?>width:235px;border-radius:20px;background:#<?php $theme !== false ? print $theme->need_help_bcolor : print '648000' ?>;position:absolute;color:#<?php $theme !== false ? print $theme->need_help_tcolor : print 'ffffff' ?>;padding:10px;border:1px solid #<?php $theme !== false ? print $theme->need_help_border : print 'dbe257' ?>;margin-top:-105px;}#<?php echo $chatCSSPrefix?>_need_help_container:hover{background-color:#<?php $theme !== false ? print $theme->need_help_hover_bg : print '84A52E' ?>}#<?php echo $chatCSSPrefix?>_need_help_container:hover #<?php echo $chatCSSPrefix?>_need_help_triangle{border-top-color:#<?php $theme !== false ? print $theme->need_help_hover_bg : print '84A52E' ?>}'+
        '#<?php echo $chatCSSPrefix?>_need_help_triangle{width: 0;height: 0;border-left: 20px solid transparent;border-right: 10px solid transparent;border-top: 15px solid #<?php $theme !== false ? print $theme->need_help_bcolor : print '648000' ?>;position:absolute;bottom:-14px;}'+
        '#<?php echo $chatCSSPrefix?>_need_help_close{cursor:pointer;float:right;border-radius:10px;background:#<?php $theme !== false ? print $theme->need_help_close_bg : print '435A05' ?>;padding:0px 6px;color:#FFF;right:10px;font-size:16px;font-weight:bold;text-decoration:none;margin-top:0px;line-height:20px}#<?php echo $chatCSSPrefix?>_need_help_close:hover{background-color:#<?php $theme !== false ? print $theme->need_help_close_hover_bg : print '74990F' ?>;}'+
        '#<?php echo $chatCSSPrefix?>_need_help_image{padding-right:10px;float:left;cursor:pointer;}#<?php echo $chatCSSPrefix?>_need_help_image img{border-radius:30px;border:1px solid #d0d0d0}#<?php echo $chatCSSPrefix?>_need_help_main_title{font-size:16px;font-weight:bold;cursor:pointer;line-height:1.5}#<?php echo $chatCSSPrefix?>_need_help_sub_title{cursor:pointer;line-height:1.5}';
	<?php else : ?>
        var raw_css_need_hl = '#<?php echo $chatCSSPrefix?>_need_help_container{<?php if ($theme !== false && $theme->show_need_help_delay > 0) : ?>display:none;<?php endif;?>width:235px;border-radius:30px;background:#<?php $theme !== false ? print $theme->need_help_bcolor : print '0c8fc4' ?>;position:absolute;color:#<?php $theme !== false ? print $theme->need_help_tcolor : print 'ffffff' ?>;padding:10px;border:1px solid #<?php $theme !== false ? print $theme->need_help_border : print '3ca5d0' ?>;margin-top:-105px;}#<?php echo $chatCSSPrefix?>_need_help_container:hover{background-color:#<?php $theme !== false ? print $theme->need_help_hover_bg : print '3ca5d0' ?>}#<?php echo $chatCSSPrefix?>_need_help_container:hover #<?php echo $chatCSSPrefix?>_need_help_triangle:before{border-top-color:#<?php $theme !== false ? print $theme->need_help_hover_bg : print '3ca5d0' ?>}'+
        '#<?php echo $chatCSSPrefix?>_need_help_triangle{width: 0;height: 0;border-left: 20px solid transparent;border-right: 10px solid transparent;border-top: 16px solid #<?php $theme !== false ? print $theme->need_help_border : print '3ca5d0' ?>;position:absolute;bottom:-17px;}'+
        '#<?php echo $chatCSSPrefix?>_need_help_triangle:before{content: \'\';display: block; position: absolute; width: 0;left:-20px; height: 0;width: 0;height: 0;border-left: 20px solid transparent;border-right: 10px solid transparent;border-top: 16px solid #<?php $theme !== false ? print $theme->need_help_bcolor : print '0c8fc4' ?>;position:absolute;bottom:1px;}'+
        '#<?php echo $chatCSSPrefix?>_need_help_close{cursor:pointer;float:right;border-radius:16px;background:#<?php $theme !== false ? print $theme->need_help_close_bg : print '000' ?>;padding:1px 7px 0px 6px;color:#FFF;right:10px;font-size:16px;font-weight:bold;text-decoration:none;margin-top:-5px;line-height:21px}#<?php echo $chatCSSPrefix?>_need_help_close:hover{background-color:#<?php $theme !== false ? print $theme->need_help_close_hover_bg : print '444' ?>;}'+
        '#<?php echo $chatCSSPrefix?>_need_help_image{padding-right:10px;float:left;cursor:pointer;}#<?php echo $chatCSSPrefix?>_need_help_image img{border-radius:30px;border:1px solid #d0d0d0}#<?php echo $chatCSSPrefix?>_need_help_main_title{font-size:16px;font-weight:bold;cursor:pointer;line-height:1.5}#<?php echo $chatCSSPrefix?>_need_help_sub_title{cursor:pointer;line-height:1.5}';
    <?php endif; ?>

    this.addCss(raw_css_need_hl);
    <?php if ($theme !== false && $theme->show_need_help_delay > 0) : ?>
    setTimeout(function(){
        var needHelp = document.getElementById("<?php echo $chatCSSPrefix?>_need_help_container");
        if (needHelp !== null){
            needHelp.style.display = "inline-block";
        }
    },<?php echo $theme->show_need_help_delay*1000?>);
    <?php endif ?>
};
};

<?php endif;?>