var titleText = (typeof <?php echo $chatOptionsVariable;?>.opt.nh_title_text != 'undefined') ? <?php echo $chatOptionsVariable;?>.opt.nh_title_text : <?php if ($theme !== false && $theme->need_help_header !== '') : print json_encode($theme->need_help_header); else : ?><?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Need help?'),ENT_QUOTES))?><?php endif;?>;
var subTitleText = (typeof <?php echo $chatOptionsVariable;?>.opt.nh_sub_title_text != 'undefined') ? <?php echo $chatOptionsVariable;?>.opt.nh_sub_title_text : <?php if ($theme !== false && $theme->need_help_text !== '') : print json_encode($theme->need_help_text); else : ?><?php echo json_encode(htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Our staff are always ready to help'),ENT_QUOTES))?><?php endif;?>;
<?php $iconsStatuses['need_help_image_url'] = ($theme !== false && $theme->need_help_image_url !== false && strpos($theme->need_help_image_url, 'http') !== false); ?>
var imageTooltip = (typeof <?php echo $chatOptionsVariable;?>.opt.nh_image != 'undefined') ? <?php echo $chatOptionsVariable;?>.opt.nh_image : '<?php if ($iconsStatuses['need_help_image_url'] == false) : ?><?php echo erLhcoreClassModelChatConfig::fetch('explicit_http_mode')->current_value?>//<?php echo $_SERVER['HTTP_HOST']?><?php endif;?><?php if ($theme !== false && $theme->need_help_image_url !== false) : print $theme->need_help_image_url; else : ?><?php echo erLhcoreClassDesign::design('images/general/operator.png');?><?php endif;?>';

subStatus = '<div id="<?php echo $chatCSSPrefix?>_need_help_container" style="<?php echo $currentPosition['nh_hor_pos']?>">'+
'<span id="<?php echo $chatCSSPrefix?>_need_help_triangle" style="<?php echo $currentPosition['nh_tr_pos']?>"></span>'+
'<i id="<?php echo $chatCSSPrefix?>_need_help_close" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" href="#">&#xd7;</i>';
if (imageTooltip !== false) {
subStatus += '<div id="<?php echo $chatCSSPrefix?>_need_help_image"><img width="60" alt="" height="60" src="' + imageTooltip + '"></div>';
};
subStatus += '<div id="<?php echo $chatCSSPrefix?>_need_help_main_title">'+titleText+'</div>'+
'<span id="<?php echo $chatCSSPrefix?>_need_help_sub_title">'+subTitleText+'</span>'+
'</div>';};