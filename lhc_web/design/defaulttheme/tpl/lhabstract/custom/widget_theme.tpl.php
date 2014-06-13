<div class="row">
	<div class="columns large-8">
		<?php if (isset($errors)) : ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
		<?php endif; ?>
		
		<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/abstract_form','Updated!'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
		<?php endif; ?>

		<?php $fields = $object->getFields();?>
		
		<div class="section-container auto" data-section data-options="deep_linking: true">	
			  <section class="active">
			    <p class="title" data-section-title><a href="#statuswidget"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Status widget style');?></a></p>
			    <div class="content" data-section-content data-slug="statuswidget">
						<label><?php echo $fields['name']['trans'];?>*</label>
						<?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object)?>
																		
						<label><?php echo $fields['online_text']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('online_text', $fields['online_text'], $object)?>
						
						<label><?php echo $fields['offline_text']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('offline_text', $fields['offline_text'], $object)?>
						
						<label><?php echo $fields['logo_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('logo_image', $fields['logo_image'], $object)?>
												
						<label><?php echo $fields['onl_bcolor']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('onl_bcolor', $fields['onl_bcolor'], $object)?>
						
						<label><?php echo $fields['text_color']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('text_color', $fields['text_color'], $object)?>
						
						<label><?php echo $fields['online_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('online_image', $fields['online_image'], $object)?>
						
						<label><?php echo $fields['offline_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('offline_image', $fields['offline_image'], $object)?>
						
			    </div>
			  </section>	
			  	
			  <section class="active">
			    <p class="title" data-section-title><a href="#widgetcontainer"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget container');?></a></p>
			    <div class="content" data-section-content data-slug="widgetcontainer">			    
						<label><?php echo $fields['header_background']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('header_background', $fields['header_background'], $object)?>												
			    </div>
			  </section>
			  	
			  <section class="active">
			    <p class="title" data-section-title><a href="#needhelp"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help widget');?></a></p>
			    <div class="content" data-section-content data-slug="needhelp">	
			    		    
						<label><?php echo $fields['need_help_header']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_header', $fields['need_help_header'], $object)?>		
			    		    
						<label><?php echo $fields['need_help_text']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_text', $fields['need_help_text'], $object)?>		
			    		    
						<label><?php echo $fields['need_help_bcolor']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_bcolor', $fields['need_help_bcolor'], $object)?>		
																
						<label><?php echo $fields['need_help_hover_bg']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_hover_bg', $fields['need_help_hover_bg'], $object)?>												
																
						<label><?php echo $fields['need_help_tcolor']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_tcolor', $fields['need_help_tcolor'], $object)?>												
																
						<label><?php echo $fields['need_help_border']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_border', $fields['need_help_border'], $object)?>												
																
						<label><?php echo $fields['need_help_close_bg']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_close_bg', $fields['need_help_close_bg'], $object)?>												
																
						<label><?php echo $fields['need_help_close_hover_bg']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_close_hover_bg', $fields['need_help_close_hover_bg'], $object)?>												
																
						<label><?php echo $fields['need_help_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_image', $fields['need_help_image'], $object)?>												
			    </div>
			  </section>
			  			  
			  <section class="active">
			    <p class="title" data-section-title><a href="#customcss"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom CSS');?></a></p>
			    <div class="content" data-section-content data-slug="customcss">			    
						<label><?php echo $fields['custom_status_css']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('custom_status_css', $fields['custom_status_css'], $object)?>		
																
						<label><?php echo $fields['custom_container_css']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('custom_container_css', $fields['custom_container_css'], $object)?>												
																
						<label><?php echo $fields['custom_widget_css']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('custom_widget_css', $fields['custom_widget_css'], $object)?>												
			    </div>
			  </section>			  
	  	</div>
	  	
	  	<ul class="button-group radius">
			<li><input type="submit" class="small button" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/></li>
			<li><input type="submit" class="small button" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/></li>
			<li><input type="submit" class="small button" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/></li>
		</ul>
		
	</div>
	<div class="columns large-4">
	<br/>
	
	<div class="row">
		<div class="columns small-12">
			<div id="lhc_container"><div id="lhc_header"><span id="lhc_title"><a title="Powered by Live Helper Chat" href="http://livehelperchat.com" target="_blank"><img src="<?php echo erLhcoreClassDesign::design('images/general/logo_grey.png');?>" alt="Live Helper Chat"></a></span><a href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" id="lhc_close"><img src="<?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>"></a>&nbsp;<a target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chat/startchat')?>/(leaveamessage)/true<?php echo $object->id > 0 ? '/(theme)/'.$object->id : ''?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Open in a new window')?>" id="lhc_remote_window"><img src="<?php echo erLhcoreClassDesign::design('images/icons/application_double.png');?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Open in a new window')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Open in a new window')?>"></a><a href="#" id="lhc_min" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Minimize/Restore')?>"><img src="<?php echo erLhcoreClassDesign::design('images/icons/min.png');?>"></a></div><div id="lhc_iframe_container"><iframe id="lhc_iframe" allowtransparency="true" scrolling="no" class="lhc-loading" frameborder="0" src="<?php echo erLhcoreClassDesign::baseurl('chat/chatwidget')?>/(leaveamessage)/true<?php echo $object->id > 0 ? '/(theme)/'.$object->id : ''?>" width="320" height="292" style="width: 100%; height: 292px;"></iframe></div></div>
			<hr>
		</div>
		<div class="columns small-12">
			<div id="lhc_status_container"><a id="online-icon" class="status-icon" href="#">{{ngModelAbstractInput_online_text || '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Live help is online...')?>'}}</a></div>
			<hr>
		</div>
		<div class="columns small-12">
			<div id="lhc_need_help_container"><a id="lhc_need_help_close" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" onclick="return lh_inst.lhc_need_help_hide();" href="#">×</a><div id="lhc_need_help_image"><img width="60" height="60" src="<?php if ($object->need_help_image_url != '') : ?><?php echo $object->need_help_image_url?><?php else : ?><?php echo erLhcoreClassDesign::design('images/general/operator.png');?><?php endif;?>"></div><div onclick="return lh_inst.lhc_need_help_click();" id="lhc_need_help_main_title">{{ngModelAbstractInput_need_help_header || '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Need help?')?>'}}</div><span id="lhc_need_help_sub_title">{{ngModelAbstractInput_need_help_text || '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Our staff is always ready to help')?>'}}</span></div>
			<hr>
		</div>
	</div>
	
		<style type="text/css">
		#lhc_status_container * {direction:ltr;text-align:left;;font-family:arial;font-size:12px;box-sizing: content-box;zoom:1;margin:0;padding:0}
		#lhc_status_container .status-icon{text-decoration:none;font-size:12px;font-weight:bold;color:#{{bactract_bg_color_text_color}};display:block;padding:10px 10px 10px 35px;background:url('<?php if ($object->online_image_url != '') : ?><?php echo $object->online_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/user_green_chat.png');?><?php endif?>') no-repeat left center}
		#lhc_status_container:hover{}
		#lhc_status_container #offline-icon{background-image:url('<?php if ($object->offline_image_url != '') : ?><?php echo $object->offline_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/user_gray_chat.png');?><?php endif;?>')}
		#lhc_status_container{box-sizing: content-box;-webkit-border-top-left-radius: 20px;-moz-border-radius-topleft: 20px;border-top-left-radius: 20px;-webkit-box-shadow: -1px -1px 5px rgba(50, 50, 50, 0.17);border:1px solid #e3e3e3;border-right:0;border-bottom:0;;-moz-box-shadow:-1px -1px 5px rgba(50, 50, 50, 0.17);box-shadow: -1px -1px 5px rgba(50, 50, 50, 0.17);padding:5px 0px 0px 5px;width:190px;font-family:arial;font-size:12px;transition: 1s;background-color:#{{bactract_bg_color_onl_bcolor}};z-index:9989;}
		@media only screen and (max-width : 640px) {#lhc_status_container{position:relative;top:0;right:0;bottom:0;left:0;width:auto;border-radius:2px;box-shadow:none;border:1px solid #e3e3e3;margin-bottom:5px;}}
		</style>
						
		<style type="text/css">
		.lhc-no-transition{ -webkit-transition: none !important; -moz-transition: none !important;-o-transition: none !important;-ms-transition: none !important;transition: none !important;}
		.lhc-min{height:35px !important}
		#lhc_container * {direction:ltr;text-align:left;;font-family:arial;font-size:12px;line-height:100%;box-sizing: content-box;-moz-box-sizing:content-box;padding:0;margin:0;}
		#lhc_container img {border:0;}
		#lhc_title{float:left;}
		#lhc_header{position:relative;z-index:9990;height:15px;overflow:hidden;text-align:right;clear:both;background-color:#{{bactract_bg_color_header_background}};padding:5px;}
		#lhc_remote_window,#lhc_min,#lhc_close{padding:2px;float:right;}
		#lhc_close:hover,#lhc_min:hover,#lhc_remote_window:hover{opacity:0.4;}
		#lhc_container {background-color:#FFF;-moz-user-select:none; -khtml-user-drag:element;cursor: move;cursor: -moz-grab;cursor: -webkit-grab;overflow: hidden;transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;z-index:9990;-webkit-box-shadow: -1px -1px 5px rgba(50, 50, 50, 0.17);-moz-box-shadow: -1px -1px 5px rgba(50, 50, 50, 0.17);box-shadow: -1px -1px 5px rgba(50, 50, 50, 0.17);-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px; }
		#lhc_container iframe{transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;}
		#lhc_container #lhc_iframe_container{border: 1px solid #ccc;border-top: 0;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;overflow: hidden;}
		#lhc_container iframe.lhc-loading{background: #FFF url(<?php echo erLhcoreClassDesign::design('images/general/loading.gif');?>) no-repeat center center; }
		@media only screen and (max-width : 640px) {#lhc_container{margin-bottom:5px;position:relative;right:0 !important;bottom:0 !important;top:0 !important}#lhc_container iframe{width:100% !important}}
		</style>
				
		<style type="text/css">
		#lhc_need_help_container{font-size:12px;width:235px;border-radius:20px;background:#{{bactract_bg_color_need_help_bcolor}};color:#{{bactract_bg_color_need_help_tcolor}};padding:10px;border:1px solid #{{bactract_bg_color_need_help_border}};}
		#lhc_need_help_container:hover{background-color:#{{bactract_bg_color_need_help_hover_bg}} }#lhc_need_help_container:hover #lhc_need_help_triangle{border-top-color:#84A52E}
		#lhc_need_help_triangle{width: 0;height: 0;border-left: 20px solid transparent;border-right: 10px solid transparent;border-top: 15px solid #{{bactract_bg_color_offl_bcolor}};position:absolute;bottom:-14px;}
		#lhc_need_help_close{float:right;border-radius:10px;background:#{{bactract_bg_color_need_help_close_bg}};padding:0px 6px;color:#FFF;right:10px;font-size:16px;font-weight:bold;text-decoration:none;margin-top:0px;line-height:20px}#lhc_need_help_close:hover{background-color:#{{bactract_bg_color_need_help_close_hover_bg}};}
		#lhc_need_help_image{padding-right:10px;float:left;cursor:pointer;}#lhc_need_help_image img{border-radius:30px;border:1px solid #d0d0d0}#lhc_need_help_main_title{font-size:16px;font-weight:bold;cursor:pointer;line-height:1.5}#lhc_need_help_sub_title{cursor:pointer;line-height:1.5}
		</style>
			
		
	</div>
</div>