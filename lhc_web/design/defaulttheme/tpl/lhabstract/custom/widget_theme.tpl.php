<?php if ($object->id != null) : ?>
<a href="<?php echo erLhcoreClassDesign::baseurl('theme/export')?>/<?php echo $object->id?>" class="pull-right btn btn-success btn-md"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Download theme')?></a>
<?php endif;?>

<div class="row">
	<div class="col-md-8">
		<?php if (isset($errors)) : ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
		<?php endif; ?>

		<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/abstract_form','Updated!'); ?>
			<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
		<?php endif; ?>

		<?php $fields = $object->getFields();?>

		<div role="tabpanel">
        	<!-- Nav tabs -->
        	<ul class="nav nav-tabs" role="tablist">
        		<li role="presentation" class="active"><a href="#statuswidget" aria-controls="statuswidget" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Status widget style');?></a></li>
        		<li role="presentation"><a href="#widgetcontainer" aria-controls="widgetcontainer" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget container');?></a></li>
        		<li role="presentation"><a href="#messagesstyle" aria-controls="messagesstyle" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Messages style');?></a></li>
        		<li role="presentation"><a href="#needhelp" aria-controls="needhelp" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help widget');?></a></li>
        		<li role="presentation"><a href="#widgettexts" aria-controls="widgettexts" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Chat widget');?></a></li>
        		<li role="presentation"><a href="#customcss" aria-controls="customcss" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom CSS');?></a></li>
        	</ul>
        
        	<!-- Tab panes -->
        	<div class="tab-content">
        		<div role="tabpanel" class="tab-pane active" id="statuswidget">
        		        <div class="form-group">
						<label><?php echo $fields['name']['trans'];?>*</label>
						<?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object)?>
						</div>
												
						<div class="form-group">		
						<label><?php echo $fields['online_text']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('online_text', $fields['online_text'], $object)?>
						</div>
						
						<div class="form-group">
						<label><?php echo $fields['offline_text']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('offline_text', $fields['offline_text'], $object)?>
						</div>
						
						<div class="form-group">						
						<label><?php echo $fields['intro_operator_text']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('intro_operator_text', $fields['intro_operator_text'], $object)?>
						</div>
						
						<div class="form-group">						
						<label><?php echo $fields['logo_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('logo_image', $fields['logo_image'], $object)?>
						</div>
						
						<div class="form-group">	
						<label><?php echo $fields['widget_copyright_url']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('widget_copyright_url', $fields['widget_copyright_url'], $object)?>
						</div>
						
						<div class="form-group">	
						<label><?php echo $fields['name_company']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('name_company', $fields['name_company'], $object)?>
						</div>
						
						<div class="form-group">					
						<label><?php echo $fields['onl_bcolor']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('onl_bcolor', $fields['onl_bcolor'], $object)?>
						</div>
						
						<div class="form-group">
						<label><?php echo $fields['text_color']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('text_color', $fields['text_color'], $object)?>
						</div>
						
						<div class="form-group">
						<label><?php echo $fields['bor_bcolor']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('bor_bcolor', $fields['bor_bcolor'], $object)?>
						</div>
						
						<div class="form-group">
						<label><?php echo $fields['online_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('online_image', $fields['online_image'], $object)?>
						</div>
						
						<div class="form-group">
						<label><?php echo $fields['offline_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('offline_image', $fields['offline_image'], $object)?>
						</div>
						
						<div class="form-group">
						<label><?php echo $fields['operator_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('operator_image', $fields['operator_image'], $object)?>
						</div>		
										
						<div class="form-group">
						<label><?php echo $fields['explain_text']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('explain_text', $fields['explain_text'], $object)?>
						</div>
						
						<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/widget_theme_status.tpl.php'));?>
						
        		</div>        		
        		<div role="tabpanel" class="tab-pane" id="messagesstyle">
                	    <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Live preview')?></h3>
                	   
            		    <div id="messages">
                            <div class="msgBlock" style="" id="messagesBlock">       
                                <div class="message-row response" style="background-color: #{{bactract_bg_color_buble_visitor_background}};color:#{{bactract_bg_color_buble_visitor_text_color}}" id="msg-10459" data-op-id="0"><div class="msg-date">10:14:39</div><span style="color:#{{bactract_bg_color_buble_visitor_title_color}}" class="usr-tit vis-tit" role="button"><i class="material-icons chat-operators mi-fs15 mr-0">face</i>Visitor</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
            		            <div class="message-row message-admin operator-changes" style="background-color: #{{bactract_bg_color_buble_operator_background}};color:#{{bactract_bg_color_buble_operator_text_color}}" id="msg-10463" data-op-id="1">
            		            <div class="msg-date">10:18:22</div><span style="color:#{{bactract_bg_color_buble_operator_title_color}}"  class="usr-tit op-tit"><i class="material-icons chat-operators mi-fs15 mr-0">account_box</i>Operator</span>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
            		        </div>
                        </div>
		
        		        <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Visitor messages style')?></h3>
        		        
        		        <div class="form-group">
        		        <label><?php echo $fields['buble_visitor_background']['trans'];?></label>
    					<?php echo erLhcoreClassAbstract::renderInput('buble_visitor_background', $fields['buble_visitor_background'], $object)?>		
    				    </div>
        		
        		        <div class="form-group">
        		        <label><?php echo $fields['buble_visitor_title_color']['trans'];?></label>
    					<?php echo erLhcoreClassAbstract::renderInput('buble_visitor_title_color', $fields['buble_visitor_title_color'], $object)?>		
    				    </div>
        		
        		        <div class="form-group">
        		        <label><?php echo $fields['buble_visitor_text_color']['trans'];?></label>
    					<?php echo erLhcoreClassAbstract::renderInput('buble_visitor_text_color', $fields['buble_visitor_text_color'], $object)?>		
    				    </div>
    				    
    				    <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator messages style')?></h3>
    				    
        		        <div class="form-group">
        		        <label><?php echo $fields['buble_operator_background']['trans'];?></label>
    					<?php echo erLhcoreClassAbstract::renderInput('buble_operator_background', $fields['buble_operator_background'], $object)?>		
    				    </div>
    				    
        		        <div class="form-group">
        		        <label><?php echo $fields['buble_operator_title_color']['trans'];?></label>
    					<?php echo erLhcoreClassAbstract::renderInput('buble_operator_title_color', $fields['buble_operator_title_color'], $object)?>		
    				    </div>
    				    
        		        <div class="form-group">
        		        <label><?php echo $fields['buble_operator_text_color']['trans'];?></label>
    					<?php echo erLhcoreClassAbstract::renderInput('buble_operator_text_color', $fields['buble_operator_text_color'], $object)?>		
    				    </div>

        		        <div class="form-group">
        		            <label><?php echo erLhcoreClassAbstract::renderInput('hide_ts', $fields['hide_ts'], $object)?> <?php echo $fields['hide_ts']['trans'];?></label>
    				    </div>

        		</div>
        		<div role="tabpanel" class="tab-pane" id="widgetcontainer">
        		
        		        <div class="form-group">
        		        <label><?php echo $fields['header_background']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('header_background', $fields['header_background'], $object)?>		
					    </div>
					    
					    <div class="form-group">				
						<label><?php echo $fields['widget_border_color']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('widget_border_color', $fields['widget_border_color'], $object)?>		
						</div>
						
						<div class="form-group">											
						<label><?php echo $fields['widget_border_width']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('widget_border_width', $fields['widget_border_width'], $object)?>		
						</div>
						
						<div class="form-group">	
						<label><?php echo $fields['header_height']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('header_height', $fields['header_height'], $object)?>		
						</div>
						
						<div class="form-group">	
						<label><?php echo $fields['header_padding']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('header_padding', $fields['header_padding'], $object)?>		
						</div>
						
						<?php /*
						<div class="form-group">							
						<label><?php echo $fields['copyright_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('copyright_image', $fields['copyright_image'], $object)?>
						</div>
						*/ ?>
						
						<div class="form-group">	
						<label><?php echo $fields['minimize_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('minimize_image', $fields['minimize_image'], $object)?>
						</div>
						
						<div class="form-group">	
						<label><?php echo $fields['restore_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('restore_image', $fields['restore_image'], $object)?>
						</div>
						
						<div class="form-group">	
						<label><?php echo $fields['close_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('close_image', $fields['close_image'], $object)?>
						</div>
						
						<div class="form-group">	
						<label><?php echo $fields['popup_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('popup_image', $fields['popup_image'], $object)?>
						</div>

						<div class="form-group">
						<label><?php echo $fields['widget_response_width']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('widget_response_width', $fields['widget_response_width'], $object)?>
						</div>
						
						<div class="form-group">	
						<label><?php echo erLhcoreClassAbstract::renderInput('show_copyright', $fields['show_copyright'], $object)?> <?php echo $fields['show_copyright']['trans'];?></label>	
						</div>
						
						<div class="form-group">	
						<label><?php echo erLhcoreClassAbstract::renderInput('hide_close', $fields['hide_close'], $object)?> <?php echo $fields['hide_close']['trans'];?></label>
						</div>
						
						<div class="form-group">	
						<label><?php echo erLhcoreClassAbstract::renderInput('hide_popup', $fields['hide_popup'], $object)?> <?php echo $fields['hide_popup']['trans'];?></label>	
        		        </div>
        		
        		</div>
        		<div role="tabpanel" class="tab-pane" id="needhelp">

        		        <div class="form-group">
        		        <label><?php echo erLhcoreClassAbstract::renderInput('show_need_help', $fields['show_need_help'], $object)?><?php echo $fields['show_need_help']['trans'];?></label>
						</div>

        		        <div class="form-group">
        		        <label><?php echo $fields['show_need_help_timeout']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('show_need_help_timeout', $fields['show_need_help_timeout'], $object)?>
						</div>

        		        <div class="form-group">
        		        <label><?php echo $fields['need_help_header']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_header', $fields['need_help_header'], $object)?>
						</div>

			    		<div class="form-group">    
						<label><?php echo $fields['need_help_text']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_text', $fields['need_help_text'], $object)?>		
			    		</div>
			    		
			    		<div class="form-group">    
						<label><?php echo $fields['need_help_bcolor']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_bcolor', $fields['need_help_bcolor'], $object)?>		
						</div>
						
						<div class="form-group">										
						<label><?php echo $fields['need_help_hover_bg']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_hover_bg', $fields['need_help_hover_bg'], $object)?>												
						</div>
						
						<div class="form-group">										
						<label><?php echo $fields['need_help_tcolor']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_tcolor', $fields['need_help_tcolor'], $object)?>												
						</div>
						
						<div class="form-group">										
						<label><?php echo $fields['need_help_border']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_border', $fields['need_help_border'], $object)?>												
						</div>
						
						<div class="form-group">										
						<label><?php echo $fields['need_help_close_bg']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_close_bg', $fields['need_help_close_bg'], $object)?>												
						</div>
						
						<div class="form-group">										
						<label><?php echo $fields['need_help_close_hover_bg']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_close_hover_bg', $fields['need_help_close_hover_bg'], $object)?>												
						</div>
						
						<div class="form-group">										
						<label><?php echo $fields['need_help_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('need_help_image', $fields['need_help_image'], $object)?>		
						</div>
        		</div>
        		
        		<div role="tabpanel" class="tab-pane" id="widgettexts">
        		
        		    <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','General settings')?></h3>
        		    
        		    <div class="form-group">										
					<label><?php echo $fields['show_voting']['trans'];?></label>
					<?php echo erLhcoreClassAbstract::renderInput('show_voting', $fields['show_voting'], $object)?>		
					</div>
					
        		    <div class="form-group">										
					<label><?php echo $fields['department_title']['trans'];?></label>
					<?php echo erLhcoreClassAbstract::renderInput('department_title', $fields['department_title'], $object)?>		
					</div>
					
        		    <div class="form-group">										
					<label><?php echo $fields['department_select']['trans'];?></label>
					<?php echo erLhcoreClassAbstract::renderInput('department_select', $fields['department_select'], $object)?>		
					</div>
					        		    
        		    <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text changes')?></h3>
        		
            		<div class="form-group">										
    				<label><?php echo $fields['support_joined']['trans'];?></label>
    				<?php echo erLhcoreClassAbstract::renderInput('support_joined', $fields['support_joined'], $object)?>		
    				</div>
    				
    				<div class="form-group">										
    				<label><?php echo $fields['support_closed']['trans'];?></label>
    				<?php echo erLhcoreClassAbstract::renderInput('support_closed', $fields['support_closed'], $object)?>		
    				</div>
    				
    				<div class="form-group">										
    				<label><?php echo $fields['pending_join']['trans'];?></label>
    				<?php echo erLhcoreClassAbstract::renderInput('pending_join', $fields['pending_join'], $object)?>		
    				</div>
    				
    				<div class="form-group">										
    				<label><?php echo $fields['noonline_operators']['trans'];?></label>
    				<?php echo erLhcoreClassAbstract::renderInput('noonline_operators', $fields['noonline_operators'], $object)?>		
    				</div>
    				
    				<div class="form-group">										
    				<label><?php echo $fields['noonline_operators_offline']['trans'];?></label>
    				<?php echo erLhcoreClassAbstract::renderInput('noonline_operators_offline', $fields['noonline_operators_offline'], $object)?>		
    				</div>
    				
        		</div>
        		
        		<div role="tabpanel" class="tab-pane" id="customcss">
        		
        		<label><?php echo $fields['custom_status_css']['trans'];?></label>
						<div class="form-group">
						<?php echo erLhcoreClassAbstract::renderInput('custom_status_css', $fields['custom_status_css'], $object)?>		
						</div>
						
						<div class="form-group">										
						<label><?php echo $fields['custom_container_css']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('custom_container_css', $fields['custom_container_css'], $object)?>												
						</div>
						
						<div class="form-group">										
						<label><?php echo $fields['custom_widget_css']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('custom_widget_css', $fields['custom_widget_css'], $object)?>	
						</div>	
						
						<div class="form-group">										
						<label><?php echo $fields['custom_popup_css']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('custom_popup_css', $fields['custom_popup_css'], $object)?>	
						</div>	
						
        		</div>
        	</div>
        </div>
			  	
	  	<div class="btn-group" role="group" aria-label="...">
			<input type="submit" class="btn btn-default" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
			<input type="submit" class="btn btn-default" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
			<input type="submit" class="btn btn-default" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
		</div>
		
	</div>
	<div class="col-md-4">
	<br/>
	
	<div class="row">
		<div class="col-md-12">
			<div id="lhc_container"><div id="lhc_header"><span id="lhc_title" ng-show="abstract_checked_show_copyright"><a title="Powered by Live Helper Chat" href="{{ngModelAbstractInput_widget_copyright_url || 'http://livehelperchat.com'}}" target="_blank"><img src="<?php if ($object->copyright_image_url != '') : ?><?php echo $object->copyright_image_url?><?php else : ?><?php echo erLhcoreClassDesign::design('images/general/logo_grey.png');?><?php endif?>" alt="Live Helper Chat"></a></span><a ng-hide="abstract_checked_hide_close" href="#" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" id="lhc_close"><img src="<?php if ($object->close_image_url != '') : ?><?php echo $object->close_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/cancel.png');?><?php endif;?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>"></a>&nbsp;<a ng-hide="abstract_checked_hide_popup" target="_blank" href="<?php echo erLhcoreClassDesign::baseurl('chat/startchat')?>/(leaveamessage)/true<?php echo $object->id > 0 ? '/(theme)/'.$object->id : ''?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Open in a new window')?>" id="lhc_remote_window"><img src="<?php if ($object->popup_image_url != '') : ?><?php echo $object->popup_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/application_double.png');?><?php endif;?>" alt="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Open in a new window')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Open in a new window')?>"></a><a href="#" id="lhc_min" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Minimize/Restore')?>"><img src="<?php if ($object->minimize_image_url != '') : ?><?php echo $object->minimize_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/min.png');?><?php endif;?>"></a><a href="#" id="lhc_min" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Minimize/Restore')?>"><img src="<?php if ($object->restore_image_url != '') : ?><?php echo $object->restore_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/restore.png');?><?php endif;?>"></a></div><div id="lhc_iframe_container"><iframe id="lhc_iframe" allowtransparency="true" scrolling="no" class="lhc-loading" frameborder="0" src="<?php echo erLhcoreClassDesign::baseurl('chat/chatwidget')?>/(sdemo)/true/(leaveamessage)/true<?php echo $object->id > 0 ? '/(theme)/'.$object->id : ''?>" width="320" height="292" style="width: 100%; height: 292px;"></iframe></div></div>
			<hr>
		</div>
		<div class="col-md-12">
			<div id="lhc_status_container"><a id="online-icon" class="status-icon" href="#">{{ngModelAbstractInput_online_text || '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Live help is online...')?>'}}</a></div>
			<hr>
		</div>
		<div class="col-md-12">
			<div id="lhc_need_help_container"><a id="lhc_need_help_close" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Close')?>" onclick="return lh_inst.lhc_need_help_hide();" href="#">&#xd7;</a><div id="lhc_need_help_image"><img width="60" height="60" src="<?php if ($object->need_help_image_url != '') : ?><?php echo $object->need_help_image_url?><?php else : ?><?php echo erLhcoreClassDesign::design('images/general/operator.png');?><?php endif;?>"></div><div onclick="return lh_inst.lhc_need_help_click();" id="lhc_need_help_main_title">{{ngModelAbstractInput_need_help_header || '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Need help?')?>'}}</div><span id="lhc_need_help_sub_title">{{ngModelAbstractInput_need_help_text || '<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/getstatus','Our staff are always ready to help')?>'}}</span></div>
			<hr>
		</div>
	</div>
	
		<style type="text/css">
		#lhc_status_container * {direction:ltr;text-align:left;;font-family:arial;font-size:12px;box-sizing: content-box;zoom:1;margin:0;padding:0}
		#lhc_status_container .status-icon{text-decoration:none;font-size:12px;font-weight:bold;color:#{{bactract_bg_color_text_color}};display:block;padding:10px 10px 10px 35px;background:url('<?php if ($object->online_image_url != '') : ?><?php echo $object->online_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/user_green_chat.png');?><?php endif?>') no-repeat left center}
		#lhc_status_container:hover{}
		#lhc_status_container #offline-icon{background-image:url('<?php if ($object->offline_image_url != '') : ?><?php echo $object->offline_image_url;?><?php else : ?><?php echo erLhcoreClassDesign::design('images/icons/user_gray_chat.png');?><?php endif;?>')}
		#lhc_status_container{box-sizing: content-box;-webkit-border-top-left-radius: 20px;-moz-border-radius-topleft: 20px;border-top-left-radius: 20px;-webkit-box-shadow: -1px -1px 5px rgba(50, 50, 50, 0.17);border:1px solid #{{bactract_bg_color_bor_bcolor}};border-right:0;border-bottom:0;-moz-box-shadow:-1px -1px 5px rgba(50, 50, 50, 0.17);box-shadow: -1px -1px 5px rgba(50, 50, 50, 0.17);padding:5px 0px 0px 5px;width:190px;font-family:arial;font-size:12px;transition: 1s;background-color:#{{bactract_bg_color_onl_bcolor}};z-index:9989;}
		@media only screen and (max-width : 640px) {#lhc_status_container{position:relative;top:0;right:0;bottom:0;left:0;width:auto;border-radius:2px;box-shadow:none;border:1px solid #e3e3e3;margin-bottom:5px;}}
		</style>
				
		<style type="text/css">
		.lhc-no-transition{ -webkit-transition: none !important; -moz-transition: none !important;-o-transition: none !important;-ms-transition: none !important;transition: none !important;}
		.lhc-min{height:35px !important}
		#lhc_container * {direction:ltr;text-align:left;;font-family:arial;font-size:12px;line-height:100%;box-sizing: content-box;-moz-box-sizing:content-box;padding:0;margin:0;}
		#lhc_container img {border:0;}
		#lhc_title{float:left;}
		#lhc_header{position:relative;z-index:9990;height:{{ngModelAbstractInput_header_height > 0 ? ngModelAbstractInput_header_height : 15}}px;overflow:hidden;text-align:right;clear:both;background-color:#{{bactract_bg_color_header_background}};padding:{{ngModelAbstractInput_header_padding > 0 ? ngModelAbstractInput_header_padding : 5}}px;}
		#lhc_remote_window,#lhc_min,#lhc_close{padding:2px;float:right;}
		#lhc_close:hover,#lhc_min:hover,#lhc_remote_window:hover{opacity:0.4;}
		#lhc_container {background-color:#FFF;-moz-user-select:none; -khtml-user-drag:element;cursor: move;cursor: -moz-grab;cursor: -webkit-grab;overflow: hidden;transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;z-index:9990;-webkit-box-shadow: -1px -1px 5px rgba(50, 50, 50, 0.17);-moz-box-shadow: -1px -1px 5px rgba(50, 50, 50, 0.17);box-shadow: -1px -1px 5px rgba(50, 50, 50, 0.17);-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px; }
		#lhc_container iframe{transition-property: height;transition-duration: 0.4s;-webkit-transition: height 0.4s ease-in-out;transition: height 0.4s;}
		#lhc_container #lhc_iframe_container{border: {{ngModelAbstractInput_widget_border_width > 0 ? ngModelAbstractInput_widget_border_width : 1}}px solid #{{bactract_bg_color_widget_border_color}};border-top: 0;border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;overflow: hidden;}
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
			
			 
	    <script>	  
	    $('#id_AbstractInput_buble_visitor_background').change(function(){
		    document.getElementById('lhc_iframe').src = document.getElementById('lhc_iframe').src;		    
		});
	    </script>
		
	</div>
</div>