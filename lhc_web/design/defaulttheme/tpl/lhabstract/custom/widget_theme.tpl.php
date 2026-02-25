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
        	<ul class="nav nav-tabs" role="tablist" data-remember="true">
        		<li role="presentation" class="nav-item"><a class="active nav-link" href="#statuswidget" aria-controls="statuswidget" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Status widget style');?></a></li>
        		<li role="presentation" class="nav-item"><a class="nav-link" href="#widgetcontainer" aria-controls="widgetcontainer" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Widget container');?></a></li>
        		<li role="presentation" class="nav-item"><a class="nav-link" href="#messagesstyle" aria-controls="messagesstyle" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Messages style');?></a></li>
        		<li role="presentation" class="nav-item"><a class="nav-link" href="#needhelp" aria-controls="needhelp" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Need help widget');?></a></li>
        		<li role="presentation" class="nav-item"><a class="nav-link" href="#widgettexts" aria-controls="widgettexts" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Chat widget');?></a></li>
        		<li role="presentation" class="nav-item"><a class="nav-link" href="#customcontent" aria-controls="customcontent" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom content');?></a></li>
        		<li role="presentation" class="nav-item"><a class="nav-link" href="#customcss" aria-controls="customcss" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom CSS');?></a></li>
        		<li role="presentation" class="nav-item"><a class="nav-link" href="#custombot" aria-controls="custombot" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Custom bot style');?></a></li>
        		<li role="presentation" class="nav-item"><a class="nav-link" href="#customnotification" aria-controls="customnotification" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Notification');?></a></li>
        		<li role="presentation" class="nav-item"><a class="nav-link" href="#reactions" aria-controls="reactions" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Reactions');?></a></li>
        		<li role="presentation" class="nav-item"><a class="nav-link" href="#survey" aria-controls="survey" role="tab" data-bs-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Survey');?></a></li>
                <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/custom_tab_multiinclude.tpl.php'));?>
        	</ul>
        
        	<!-- Tab panes -->
        	<div class="tab-content">
        		<div role="tabpanel" class="tab-pane active" id="statuswidget">

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['name']['trans'];?>*</label>
                                    <?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object)?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>
                                        <a class="live-help-tooltip" data-bs-placement="top" title="" data-bs-toggle="tooltip" data-bs-title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','If you enter alias make sure you pass this string as argument for a theme. Otherwise argument will be ignored.');?>"><i class="material-icons">&#xE887;</i></a><?php echo $fields['alias']['trans'];?>
                                    </label>
                                    <?php echo erLhcoreClassAbstract::renderInput('alias', $fields['alias'], $object)?>
                                </div>
                            </div>
                        </div>

                    <div class="form-group">
                        <label><?php echo $fields['theme_expires']['trans'];?></label>
                        <select class="form-control form-control-sm" name="AbstractInput_theme_expires">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','Choose duration');?></option>
                            <option value="15" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 15) : ?>selected="selected"<?php endif;?> >15 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','seconds');?></option>
                            <option value="30" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 30) : ?>selected="selected"<?php endif;?> >30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','seconds');?></option>
                            <option value="60" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 60) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minute');?></option>
                            <option value="300" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 300) : ?>selected="selected"<?php endif;?> >5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
                            <option value="600" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 600) : ?>selected="selected"<?php endif;?> >10 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
                            <option value="1800" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 1800) : ?>selected="selected"<?php endif;?> >30 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','minutes');?></option>
                            <option value="3600" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 3600) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hour');?></option>
                            <option value="7200" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 7200) : ?>selected="selected"<?php endif;?> >2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
                            <option value="14400" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 14400) : ?>selected="selected"<?php endif;?> >4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
                            <option value="28800" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 28800) : ?>selected="selected"<?php endif;?> >8 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
                            <option value="57600" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 57600) : ?>selected="selected"<?php endif;?> >16 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','hours');?></option>
                            <option value="86400" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 86400) : ?>selected="selected"<?php endif;?> >1 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','day');?></option>
                            <option value="<?php echo 86400 * 2?>" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 86400 * 2) : ?>selected="selected"<?php endif;?> >2 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','days');?></option>
                            <option value="<?php echo 86400 * 3?>" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 86400 * 3) : ?>selected="selected"<?php endif;?> >3 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','days');?></option>
                            <option value="<?php echo 86400 * 4?>" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 86400 * 4) : ?>selected="selected"<?php endif;?> >4 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','days');?></option>
                            <option value="<?php echo 86400 * 5?>" <?php if (isset($object->{$fields['theme_expires']['main_attr']}['theme_expires']) && $object->{$fields['theme_expires']['main_attr']}['theme_expires'] == 86400 * 5) : ?>selected="selected"<?php endif;?> >5 <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendnotice','days');?></option>
                        </select>
                    </div>

                        <div class="form-group">
						<label><?php echo erLhcoreClassAbstract::renderInput('modern_look', $fields['modern_look'], $object)?> <?php echo $fields['modern_look']['trans'];?></label>
						</div>

                        <div class="form-group">
						<label><?php echo erLhcoreClassAbstract::renderInput('drag_enabled', $fields['drag_enabled'], $object)?> <?php echo $fields['drag_enabled']['trans'];?></label>
						</div>
                    
                        <div class="form-group">
						<label><?php echo erLhcoreClassAbstract::renderInput('load_w2', $fields['load_w2'], $object)?> <?php echo $fields['load_w2']['trans'];?></label>
						</div>

						<div class="form-group">		
						<label><?php echo $fields['online_text']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('online_text', $fields['online_text'], $object)?>
						</div>
						
						<div class="form-group">
						<label><?php echo $fields['offline_text']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('offline_text', $fields['offline_text'], $object)?>
						</div>

                        <?php $translatableItem = array('identifier' => 'intro_operator_text'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

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

                        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Online status options')?></h5>

                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label><?php echo $fields['onl_bcolor']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('onl_bcolor', $fields['onl_bcolor'], $object)?>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label><?php echo $fields['text_color']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('text_color', $fields['text_color'], $object)?>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label><?php echo $fields['bor_bcolor']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('bor_bcolor', $fields['bor_bcolor'], $object)?>
                            </div>
                        </div>
                    </div>

                        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Offline status options')?></h5>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo $fields['offl_bcolor']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('offl_bcolor', $fields['offl_bcolor'], $object)?>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo $fields['offltxt_color']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('offltxt_color', $fields['offltxt_color'], $object)?>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo $fields['offlbor_bcolor']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('offlbor_bcolor', $fields['offlbor_bcolor'], $object)?>
                                </div>
                            </div>
                        </div>


                        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Images')?></h5>
						<div class="form-group">
						<label><?php echo $fields['online_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('online_image', $fields['online_image'], $object)?>
						</div>
						
						<div class="form-group">
						<label><?php echo $fields['offline_image']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('offline_image', $fields['offline_image'], $object)?>
						</div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['operator_image']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('operator_image', $fields['operator_image'], $object)?>
                                </div>
                            </div>
                            <div class="col-6">
                                <?php $avatarOptions = ['field_name' => 'AbstractInput_operator_avatar', 'avatar' => (isset($object->bot_configuration_array['operator_avatar']) ? $object->bot_configuration_array['operator_avatar'] : '') ]; ?>
                                <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/avatar_build.tpl.php'));?>
                            </div>
                        </div>

                        <?php $translatableItem = array('identifier' => 'explain_text', 'bb_code_selected' => 'textarea[name=AbstractInput_explain_text]'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

						<div class="form-group">
						<label><?php echo $fields['show_status_delay']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('show_status_delay', $fields['show_status_delay'], $object)?>
						</div>

						<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/widget_theme_status.tpl.php'));?>
						
        		</div>        		
        		<div role="tabpanel" class="tab-pane" id="messagesstyle">
                	    <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Live preview')?></h3>

            		    <div id="messages" ng-class="{'bubble-messages': abstract_checked_bubble_style_profile, 'hide-visitor-profile' : abstract_checked_hide_visitor_profile}">
                            <div class="msgBlock" style="" id="messagesBlock">       
                                <div class="message-row response" id="msg-10459" data-op-id="0">
                                    <div class="msg-date">10:14:39</div>
                                    <span style="color:#{{bactract_bg_color_buble_visitor_title_color}}" class="usr-tit vis-tit" role="button"><i class="material-icons chat-operators mi-fs15 me-0">face</i>
                                        <span ng-hide="abstract_checked_bubble_style_profile">Visitor</span>
                                    </span>
                                    <div class="msg-body" style="background-color: #{{bactract_bg_color_buble_visitor_background}};color:#{{bactract_bg_color_buble_visitor_text_color}}">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
                                </div>
            		            <div class="message-row message-admin operator-changes" id="msg-10463" data-op-id="1">
            		                <div class="msg-date">10:18:22</div>
                                    <span style="color:#{{bactract_bg_color_buble_operator_title_color}}" class="usr-tit op-tit" >
                                        <i ng-hide="abstract_checked_bubble_style_profile" class="material-icons chat-operators mi-fs15 me-0">account_box</i>
                                        <span ng-hide="abstract_checked_bubble_style_profile" class="op-nick-title">Operator</span>

                                        <i ng-show="abstract_checked_bubble_style_profile" class="chat-operators mi-fs15 me-0">
                                            <img class="profile-msg-pic" src="<?php echo erLhcoreClassDesign::design('images/general/logo.png');?>" alt="">
                                        </i>
                                    </span>
                                    <div class="msg-body" style="color:#{{bactract_bg_color_buble_operator_text_color}};background-color: #{{bactract_bg_color_buble_operator_background}};">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
                                </div>

                                <div id="scroll-to-message" class="message-admin border-bottom border-danger text-center" style="border-bottom-style: dashed!important;border-bottom-color: #{{bactract_bg_color_bg_new_msg}}!important;"><span style="color:#{{bactract_bg_color_new_msg_text_color}}!important;background-color: #{{bactract_bg_color_bg_new_msg}}!important;" class="new-msg bg-danger text-white d-inline-block fs12 rounded-top"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','New')?></span></div>

                                <div class="message-row message-admin operator-changes" id="msg-10463" data-op-id="1">
            		                <div class="msg-date">10:18:22</div>
                                    <span style="color:#{{bactract_bg_color_buble_operator_title_color}}" class="usr-tit op-tit" >
                                        <i ng-hide="abstract_checked_bubble_style_profile" class="material-icons chat-operators mi-fs15 me-0">account_box</i>
                                        <span ng-hide="abstract_checked_bubble_style_profile" class="op-nick-title">Operator</span>

                                        <i ng-show="abstract_checked_bubble_style_profile" class="chat-operators mi-fs15 me-0">
                                            <img class="profile-msg-pic" src="<?php echo erLhcoreClassDesign::design('images/general/logo.png');?>" alt="">
                                        </i>
                                    </span>
                                    <div class="msg-body" style="color:#{{bactract_bg_color_buble_operator_text_color}};background-color: #{{bactract_bg_color_buble_operator_background}};">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
                                </div>

                                <div class="btn-bottom-scroll fade-in-fast text-center"><button type="button" style="border-radius:15px;padding: 3px 10px;opacity: 0.85;color:#{{bactract_bg_color_text_scroll_bottom}}!important;background-color: #{{bactract_bg_color_bg_scroll_bottom}}!important;" class="btn btn-sm btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','↓ Scroll to the bottom')?></button></div>

            		        </div>
                        </div>
		
        		        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Visitor messages style')?></h5>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo erLhcoreClassAbstract::renderInput('bubble_style_profile', $fields['bubble_style_profile'], $object)?> <?php echo $fields['bubble_style_profile']['trans'];?></label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo erLhcoreClassAbstract::renderInput('hide_visitor_profile', $fields['hide_visitor_profile'], $object)?> <?php echo $fields['hide_visitor_profile']['trans'];?></label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo erLhcoreClassAbstract::renderInput('msg_expand', $fields['msg_expand'], $object)?> <?php echo $fields['msg_expand']['trans'];?></label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo $fields['buble_visitor_background']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('buble_visitor_background', $fields['buble_visitor_background'], $object)?>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo $fields['buble_visitor_title_color']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('buble_visitor_title_color', $fields['buble_visitor_title_color'], $object)?>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo $fields['buble_visitor_text_color']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('buble_visitor_text_color', $fields['buble_visitor_text_color'], $object)?>
                                </div>
                            </div>
                        </div>

    				    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator messages style')?></h5>

                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo $fields['buble_operator_background']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('buble_operator_background', $fields['buble_operator_background'], $object)?>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo $fields['buble_operator_title_color']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('buble_operator_title_color', $fields['buble_operator_title_color'], $object)?>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo $fields['buble_operator_text_color']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('buble_operator_text_color', $fields['buble_operator_text_color'], $object)?>
                                </div>
                            </div>
                        </div>

                        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','New message separator style')?></h5>

                        <?php $translatableItem = array('identifier' => 'cnew_msgh'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['new_msg_text_color']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('new_msg_text_color', $fields['new_msg_text_color'], $object)?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['bg_new_msg']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('bg_new_msg', $fields['bg_new_msg'], $object)?>
                                </div>
                            </div>
                        </div>

                        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Scroll to the bottom style')?></h5>

                        <?php $translatableItem = array('identifier' => 'cnew_msg'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                        <?php $translatableItem = array('identifier' => 'cnew_msgm'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                        <?php $translatableItem = array('identifier' => 'cscroll_btn'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['bg_scroll_bottom']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('bg_scroll_bottom', $fields['bg_scroll_bottom'], $object)?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['text_scroll_bottom']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('text_scroll_bottom', $fields['text_scroll_bottom'], $object)?>
                                </div>
                            </div>
                        </div>

                        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Other')?></h5>

        		        <div class="form-group">
                            <label><?php echo $fields['hide_ts']['trans'];?></label>
                            <?php echo erLhcoreClassAbstract::renderInput('hide_ts', $fields['hide_ts'], $object)?>
    				    </div>

        		        <div class="form-group">
                            <label><?php echo $fields['hide_op_ts']['trans'];?></label>
                            <?php echo erLhcoreClassAbstract::renderInput('hide_op_ts', $fields['hide_op_ts'], $object)?>
    				    </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['msg_time_format_day']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('msg_time_format_day', $fields['msg_time_format_day'], $object)?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['msg_time_format_time']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('msg_time_format_time', $fields['msg_time_format_time'], $object)?>
                                </div>
                            </div>
                        </div>


        		        <div class="form-group">
        		            <label><?php echo erLhcoreClassAbstract::renderInput('disable_edit_prev', $fields['disable_edit_prev'], $object)?> <?php echo $fields['disable_edit_prev']['trans'];?></label>
    				    </div>

        		</div>
        		<div role="tabpanel" class="tab-pane pt-2" id="widgetcontainer">

                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo $fields['header_background']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('header_background', $fields['header_background'], $object)?>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo $fields['widget_border_color']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('widget_border_color', $fields['widget_border_color'], $object)?>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label><?php echo $fields['header_icon_color']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('header_icon_color', $fields['header_icon_color'], $object)?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo $fields['icons_order']['trans'];?></label>
                            <?php echo erLhcoreClassAbstract::renderInput('icons_order', $fields['icons_order'], $object)?>
                            <div class="text-muted fs12">E.g</div>
                            <div class="text-muted fs12">* dropdown_print,dropdown_min,dropdown_popup,dropdown_fullheight,dropdown_close</div>
                            <div class="text-muted fs12">* left_close,right_min,dropdown_popup,left_fullheight,dropdown_close</div>
                        </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                            <label><?php echo $fields['widget_border_width']['trans'];?></label>
                            <?php echo erLhcoreClassAbstract::renderInput('widget_border_width', $fields['widget_border_width'], $object)?>
                            </div>
                        </div>
                    </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['header_height']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('header_height', $fields['header_height'], $object)?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['header_padding']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('header_padding', $fields['header_padding'], $object)?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['wwidth']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('wwidth', $fields['wwidth'], $object)?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['wheight']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('wheight', $fields['wheight'], $object)?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['wright']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('wright', $fields['wright'], $object)?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['wbottom']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('wbottom', $fields['wbottom'], $object)?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['wtop']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('wtop', $fields['wtop'], $object)?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo erLhcoreClassAbstract::renderInput('fscreen_embed', $fields['fscreen_embed'], $object)?> <?php echo $fields['fscreen_embed']['trans'];?></label>
                        </div>

						<div class="form-group">
						<label><?php echo $fields['wright_inv']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('wright_inv', $fields['wright_inv'], $object)?>
						</div>

                        <input class="d-none" checked="checked" type="checkbox" name="AbstractInput_copyright_image_delete" value="1">

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
						
						<?php /*<div class="form-group">
						<label><?php echo erLhcoreClassAbstract::renderInput('show_copyright', $fields['show_copyright'], $object)?> <?php echo $fields['show_copyright']['trans'];?></label>	
						</div>*/ ?>

                        <div class="form-group">
                            <label><?php echo erLhcoreClassAbstract::renderInput('lazy_load', $fields['lazy_load'], $object)?> <?php echo $fields['lazy_load']['trans'];?></label>
                        </div>

                        <div class="form-group">
                            <label><?php echo erLhcoreClassAbstract::renderInput('close_in_status', $fields['close_in_status'], $object)?> <?php echo $fields['close_in_status']['trans'];?></label>
                        </div>

						<div class="form-group">	
						<label><?php echo erLhcoreClassAbstract::renderInput('hide_close', $fields['hide_close'], $object)?> <?php echo $fields['hide_close']['trans'];?></label>
						</div>

						<div class="form-group">
						<label><?php echo erLhcoreClassAbstract::renderInput('hide_iframe', $fields['hide_iframe'], $object)?> <?php echo $fields['hide_iframe']['trans'];?></label>
						</div>
                    
						<div class="form-group">
						<label><?php echo erLhcoreClassAbstract::renderInput('hide_parent', $fields['hide_parent'], $object)?> <?php echo $fields['hide_parent']['trans'];?></label>
						</div>
						
						<div class="form-group">	
						<label><?php echo erLhcoreClassAbstract::renderInput('hide_popup', $fields['hide_popup'], $object)?> <?php echo $fields['hide_popup']['trans'];?></label>	
        		        </div>

						<div class="form-group">
						<label><?php echo erLhcoreClassAbstract::renderInput('disable_sound', $fields['disable_sound'], $object)?> <?php echo $fields['disable_sound']['trans'];?></label>
        		        </div>

						<div class="form-group">
						<label><?php echo erLhcoreClassAbstract::renderInput('kcw', $fields['kcw'], $object)?> <?php echo $fields['kcw']['trans'];?></label>
        		        </div>

						<div class="form-group">	
						<label><?php echo erLhcoreClassAbstract::renderInput('detect_language', $fields['detect_language'], $object)?> <?php echo $fields['detect_language']['trans'];?></label>
        		        </div>

						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('survey_button', $fields['survey_button'], $object)?> <?php echo $fields['survey_button']['trans'];?></label>
        		        </div>

						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('confirm_close', $fields['confirm_close'], $object)?> <?php echo $fields['confirm_close']['trans'];?></label>
        		        </div>

						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('start_on_close', $fields['start_on_close'], $object)?> <?php echo $fields['start_on_close']['trans'];?></label>
        		        </div>

						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('prev_msg', $fields['prev_msg'], $object)?> <?php echo $fields['prev_msg']['trans'];?></label>
        		        </div>

						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('custom_html_priority', $fields['custom_html_priority'], $object)?> <?php echo $fields['custom_html_priority']['trans'];?></label>
        		        </div>

						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('proactive_once_typed', $fields['proactive_once_typed'], $object)?> <?php echo $fields['proactive_once_typed']['trans'];?></label>
        		        </div>
                    
						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('hide_job_title', $fields['hide_job_title'], $object)?> <?php echo $fields['hide_job_title']['trans'];?></label>
        		        </div>
                    
						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('job_new_row', $fields['job_new_row'], $object)?> <?php echo $fields['job_new_row']['trans'];?></label>
        		        </div>

						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('close_on_unload', $fields['close_on_unload'], $object)?> <?php echo $fields['close_on_unload']['trans'];?></label>
        		        </div>
                    
						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('dont_prefill_offline', $fields['dont_prefill_offline'], $object)?> <?php echo $fields['dont_prefill_offline']['trans'];?></label>
        		        </div>

						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('hide_bb_code', $fields['hide_bb_code'], $object)?> <?php echo $fields['hide_bb_code']['trans'];?></label>
        		        </div>

						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('msg_snippet', $fields['msg_snippet'], $object)?> <?php echo $fields['msg_snippet']['trans'];?></label>
        		        </div>

						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('font_size', $fields['font_size'], $object)?> <?php echo $fields['font_size']['trans'];?></label>
        		        </div>

						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('use_system_tz', $fields['use_system_tz'], $object)?> <?php echo $fields['use_system_tz']['trans'];?></label>
        		        </div>

						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('print_btn_msg', $fields['print_btn_msg'], $object)?> <?php echo $fields['print_btn_msg']['trans'];?></label>
        		        </div>

						<div class="form-group">
						    <label><?php echo erLhcoreClassAbstract::renderInput('same_target_link', $fields['same_target_link'], $object)?> <?php echo $fields['same_target_link']['trans'];?></label>
        		        </div>

                        <div class="form-group">
                            <label><?php echo $fields['embed_closed']['trans'];?></label>
                            <select name="AbstractInput_embed_closed" class="form-control form-control-sm">
                                <option value="" <?php !isset($object->bot_configuration_array['embed_closed']) || $object->bot_configuration_array['embed_closed'] == '' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Absent');?></option>
                                <option value="1" <?php isset($object->bot_configuration_array['embed_closed']) && $object->bot_configuration_array['embed_closed'] == '1' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Top right');?></option>
                                <option value="2" <?php isset($object->bot_configuration_array['embed_closed']) && $object->bot_configuration_array['embed_closed'] == '2' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Bottom left');?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><?php echo $fields['viewport']['trans'];?></label>
                            <select name="AbstractInput_viewport" class="form-control form-control-sm">
                                <option value="" <?php !isset($object->bot_configuration_array['viewport']) || $object->bot_configuration_array['viewport'] == '' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Always enabled');?></option>
                                <option value="1" <?php isset($object->bot_configuration_array['viewport']) && $object->bot_configuration_array['viewport'] == '1' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Enabled on mobile');?></option>
                                <option value="2" <?php isset($object->bot_configuration_array['viewport']) && $object->bot_configuration_array['viewport'] == '2' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Always off');?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label><?php echo $fields['switch_to_human']['trans'];?></label>
                            <?php echo erLhcoreClassAbstract::renderInput('switch_to_human', $fields['switch_to_human'], $object)?>
                        </div>

                        <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme', 'Override embed code settings'); ?></h3>

                        <div class="form-group">
                            <label><?php echo $fields['enable_widget_embed_override']['trans'];?></label>
                            <?php echo erLhcoreClassAbstract::renderInput('enable_widget_embed_override', $fields['enable_widget_embed_override'], $object)?>
                        </div>

                        <div class="form-group">
                            <label><?php echo erLhcoreClassAbstract::renderInput('widget_show_leave_form', $fields['widget_show_leave_form'], $object)?> <?php echo $fields['widget_show_leave_form']['trans'];?></label>
                        </div>

                        <div class="form-group">
                            <label><?php echo $fields['widget_position']['trans'];?></label>
                            <select name="AbstractInput_widget_position" class="form-control">
                                <option value="bottom_right" <?php $object->widget_position == 'bottom_right' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Bottom right corner of the screen');?></option>
                                <option value="bottom_left" <?php $object->widget_position == 'bottom_left' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Bottom left corner of the screen');?></option>
                                <option value="middle_right" <?php $object->widget_position == 'middle_right' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Middle right side of the screen');?></option>
                                <option value="middle_left" <?php $object->widget_position == 'middle_left' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Middle left side of the screen');?></option>
                                <option value="full_height_right" <?php $object->widget_position == 'full_height_right' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Full height right');?></option>
                                <option value="full_height_left" <?php $object->widget_position == 'full_height_left' ? print 'selected="selected"' : ''?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Full height left');?></option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['widget_popwidth']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('widget_popwidth', $fields['widget_popwidth'], $object)?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['widget_popheight']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('widget_popheight', $fields['widget_popheight'], $object)?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['widget_pright']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('widget_pright', $fields['widget_pright'], $object)?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label><?php echo $fields['widget_pbottom']['trans'];?></label>
                                    <?php echo erLhcoreClassAbstract::renderInput('widget_pbottom', $fields['widget_pbottom'], $object)?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo $fields['widget_survey']['trans'];?></label>
                            <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                                'input_name'     => 'AbstractInput_widget_survey',
                                'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','no survey'),
                                'selected_id'    => $object->widget_survey,
                                'css_class'     => 'form-control',
                                'list_function'  => 'erLhAbstractModelSurvey::getList'
                            )); ?>
                        </div>

        		
        		</div>
        		<div role="tabpanel" class="tab-pane pt-2" id="needhelp">

        		        <div class="form-group">
        		        <label><?php echo erLhcoreClassAbstract::renderInput('show_need_help', $fields['show_need_help'], $object)?> <?php echo $fields['show_need_help']['trans'];?></label>
						</div>

        		        <div class="form-group">
        		        <label><?php echo erLhcoreClassAbstract::renderInput('hide_mobile_nh', $fields['hide_mobile_nh'], $object)?> <?php echo $fields['hide_mobile_nh']['trans'];?></label>
						</div>

                        <div class="form-group">
        		        <label><?php echo erLhcoreClassAbstract::renderInput('animate_nh', $fields['animate_nh'], $object)?> <?php echo $fields['animate_nh']['trans'];?></label>
						</div>

        		        <div class="form-group">
        		        <label><?php echo erLhcoreClassAbstract::renderInput('always_present_nh', $fields['always_present_nh'], $object)?> <?php echo $fields['always_present_nh']['trans'];?></label>
						</div>

        		        <div class="form-group">
        		        <label><?php echo erLhcoreClassAbstract::renderInput('hide_close_nh', $fields['hide_close_nh'], $object)?> <?php echo $fields['hide_close_nh']['trans'];?></label>
						</div>

        		        <div class="form-group">
        		        <label><?php echo $fields['show_need_help_timeout']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('show_need_help_timeout', $fields['show_need_help_timeout'], $object)?>
						</div>

        		        <div class="form-group">
        		        <label><?php echo $fields['show_need_help_delay']['trans'];?></label>
						<?php echo erLhcoreClassAbstract::renderInput('show_need_help_delay', $fields['show_need_help_delay'], $object)?>
						</div>

                        <?php $translatableItem = array('identifier' => 'need_help_header'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                        <?php $translatableItem = array('identifier' => 'need_help_text'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

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

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo $fields['need_help_image']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('need_help_image', $fields['need_help_image'], $object)?>
                            </div>
                        </div>
                        <div class="col-6">
                            <?php $avatarOptions = ['field_prefix' => 'nh_', 'field_name' => 'AbstractInput_nh_avatar', 'avatar' => (isset($object->bot_configuration_array['nh_avatar']) ? $object->bot_configuration_array['nh_avatar'] : '') ]; ?>
                            <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/avatar_build.tpl.php'));?>
                        </div>
                    </div>


                    <hr>
                    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Build your own need help widget layout')?></h4>

                    <button type="button" class="btn btn-sm btn-secondary" onclick="setDefaultNeedHelp()"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Set default HTML')?></button>

<div class="row">
    <div class="col-3">
        <div class="form-group">
            <label><?php echo $fields['nh_bottom']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('nh_bottom', $fields['nh_bottom'], $object)?>
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <label><?php echo $fields['nh_right']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('nh_right', $fields['nh_right'], $object)?>
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <label><?php echo $fields['nh_height']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('nh_height', $fields['nh_height'], $object)?>
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <label><?php echo $fields['nh_width']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('nh_width', $fields['nh_width'], $object)?>
        </div>

    </div>
</div>

        <?php $translatableItem = array('identifier' => 'need_help_html'); ?>
        <p><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','If you want to get nerdy you can build your own eye catcher using default template as starting point. You can adjust need help widget dimensions above. Also see what placeholders we support.')?></small></p>
        <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>
</div>

        		
        		<div role="tabpanel" class="tab-pane" id="widgettexts">
        		
        		    <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','General settings')?></h3>
        		    
        		    <div class="form-group">										
					<label><?php echo $fields['show_voting']['trans'];?></label>
					<?php echo erLhcoreClassAbstract::renderInput('show_voting', $fields['show_voting'], $object)?>		
					</div>

        		    <div class="form-group">
					<label><?php echo $fields['hide_status']['trans'];?></label>
					<?php echo erLhcoreClassAbstract::renderInput('hide_status', $fields['hide_status'], $object)?>
					</div>

                    <?php $translatableItem = array('identifier' => 'department_title'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'department_select'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'formf_name'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'formf_email'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'formf_file'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'formf_question'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'formf_phone'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text changes')?></h3>

                    <?php $translatableItem = array('identifier' => 'placeholder_name'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'placeholder_message'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'placeholder_file_message'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'bot_status_text'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>
                    
                    <?php $translatableItem = array('identifier' => 'custom_tos_text'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'min_text'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'popup_text'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'end_chat_text'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'fheight_text_class'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'fheight_text_col'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'support_joined'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'support_closed'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'pending_join'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'pending_join_queue'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'noonline_operators'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'noonline_operators_offline'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'thank_feedback'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'blocked_visitor'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'custom_start_button'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'custom_start_button_bot'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'custom_start_button_offline'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'back_to_chat'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'custom_op_name'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'chat_unavailable'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

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

                    <div class="form-group">
                    <label><?php echo $fields['custom_page_css']['trans'];?></label>
                    <?php echo erLhcoreClassAbstract::renderInput('custom_page_css', $fields['custom_page_css'], $object)?>
                    </div>

        		</div>

                <div role="tabpanel" class="tab-pane pt-2" id="reactions">

                    <div class="form-group">
                        <label><?php echo erLhcoreClassAbstract::renderInput('enable_react_for_vi', $fields['enable_react_for_vi'], $object)?> <?php echo $fields['enable_react_for_vi']['trans'];?></label>
                    </div>

                    <div class="form-group">
                        <label><?php echo erLhcoreClassAbstract::renderInput('always_visible_reactions', $fields['always_visible_reactions'], $object)?> <?php echo $fields['always_visible_reactions']['trans'];?></label>
                    </div>

                    <div class="form-group">
                        <label><?php echo erLhcoreClassAbstract::renderInput('one_reaction_per_msg', $fields['one_reaction_per_msg'], $object)?> <?php echo $fields['one_reaction_per_msg']['trans'];?></label>
                    </div>

                    <div class="form-group">
                        <label><?php echo erLhcoreClassAbstract::renderInput('reactions_always_visible_under', $fields['reactions_always_visible_under'], $object)?> <?php echo $fields['reactions_always_visible_under']['trans'];?></label>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo $fields['buble_reaction_background_vi']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('buble_reaction_background_vi', $fields['buble_reaction_background_vi'], $object)?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo $fields['buble_reaction_color_vi']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('buble_reaction_color_vi', $fields['buble_reaction_color_vi'], $object)?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo $fields['buble_reaction_background_op']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('buble_reaction_background_op', $fields['buble_reaction_background_op'], $object)?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo $fields['buble_reaction_color_op']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('buble_reaction_color_op', $fields['buble_reaction_color_op'], $object)?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo $fields['custom_tb_reactions']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('custom_tb_reactions', $fields['custom_tb_reactions'], $object)?>
                                <div>
                                    <small>
                                        E.g 1. <br/><i>thumb_up|1|thumb|Thumbs up=thumb_down|0|thumb|Thumbs down</i><br/>
                                        E.g 2. <br/><i>😍=😙=😁=thumb_up|1|thumb|Thumbs up</i>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <?php
                                $bbcodeParams['hide_modal'] = true;
                                $bbcodeParams['tab_prefix'] = '-ct';
                                $bbcodeParams['editor_id'] = 'textarea[name=AbstractInput_custom_tb_reactions]';
                            ?>
                            <?php include(erLhcoreClassDesign::designtpl('lhchat/bbcodeinsert.tpl.php'));?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-6">
                                <label><?php echo $fields['custom_mw_reactions']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('custom_mw_reactions', $fields['custom_mw_reactions'], $object)?>
                                <div>
                                    <small>
                                        E.g 1. <br/><i>thumb_up|1|thumb|Thumbs up=thumb_down|0|thumb|Thumbs down</i><br/>
                                        E.g 2. <br/><i>😍=😙=😁=thumb_up|1|thumb|Thumbs up</i>
                                    </small>
                                </div>
                            </div>
                            <div class="col-6">
                                <?php
                                    $bbcodeParams['hide_modal'] = true;
                                    $bbcodeParams['tab_prefix'] = '-mw';
                                    $bbcodeParams['editor_id'] = 'textarea[name=AbstractInput_custom_mw_reactions]';
                                ?>
                                <?php include(erLhcoreClassDesign::designtpl('lhchat/bbcodeinsert.tpl.php'));?>
                            </div>
                        </div>
                    </div>
                    <?php
                        // If checked always visible toolbar it will be at the bottom as is now
                        // If not checked always visible it will be at the right side of the message
                        // If modal window content is filled additional icon will be shown
                    ?>
                </div>

                <div role="tabpanel" class="tab-pane" id="survey">
                    <?php $translatableItem = array('identifier' => 'survey_title'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'survey_feedback'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <div class="form-group">
                        <label><?php echo $fields['custom_survey_css']['trans'];?></label>
                        <?php echo erLhcoreClassAbstract::renderInput('custom_survey_css', $fields['custom_survey_css'], $object)?>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane" id="customcontent">

                    <?php $translatableItem = array('identifier' => 'custom_html'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'custom_html_widget'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'custom_html_bot'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'custom_html_widget_bot'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'custom_html_header'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <div class="form-group">
                        <label><?php echo erLhcoreClassAbstract::renderInput('custom_html_footer_raw', $fields['custom_html_footer_raw'], $object)?> <?php echo $fields['custom_html_footer_raw']['trans'];?></label>
                    </div>

                    <?php $translatableItem = array('identifier' => 'custom_html_footer'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>


                    <?php $translatableItem = array('identifier' => 'custom_html_header_body'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'intro_message'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>
                    
                    <?php $translatableItem = array('identifier' => 'intro_message_html'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'pre_chat_html'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'pre_offline_chat_html'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'custom_html_status'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'after_chat_status'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'inject_html'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <?php $translatableItem = array('identifier' => 'header_html'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

                    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Text content before user fields')?></h4>

                    <div class="form-group">
                        <label><?php echo erLhcoreClassAbstract::renderInput('auto_bot_intro', $fields['auto_bot_intro'], $object)?> <?php echo $fields['auto_bot_intro']['trans'];?></label>
                    </div>

                    <div class="form-group">
                        <label><?php echo erLhcoreClassAbstract::renderInput('use_bot_profile', $fields['use_bot_profile'], $object)?> <?php echo $fields['use_bot_profile']['trans'];?></label>
                    </div>

                    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Choose manually bot ant trigger')?></h5>

                    <p><i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','For it to work, trigger has to have checked')?></i>&nbsp;<span class="badge bg-info"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Can be passed as argument')?></span></p>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo $fields['bot_id']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('bot_id', $fields['bot_id'], $object)?>
                            </div>

                            <div class="form-group">
                                <label><?php echo $fields['trigger_id']['trans'];?></label>
                                <div id="trigger-list-id"><input type="hidden" value="0" name="AbstractInput_trigger_id" /></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Preview')?></label>
                            <div id="trigger-preview-window">

                            </div>
                        </div>
                    </div>

                </div>

                <div role="tabpanel" class="tab-pane" id="custombot">
                    <div class="form-group">
                        <label><?php echo $fields['bot_button_border']['trans'];?></label>
                        <?php echo erLhcoreClassAbstract::renderInput('bot_button_border', $fields['bot_button_border'], $object)?>
                    </div>
                    <div class="form-group">
                        <label><?php echo $fields['bot_button_background']['trans'];?></label>
                        <?php echo erLhcoreClassAbstract::renderInput('bot_button_background', $fields['bot_button_background'], $object)?>
                    </div>
                    <div class="form-group">
                        <label><?php echo $fields['bot_button_background_hover']['trans'];?></label>
                        <?php echo erLhcoreClassAbstract::renderInput('bot_button_background_hover', $fields['bot_button_background_hover'], $object)?>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo $fields['bot_button_text_color']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('bot_button_text_color', $fields['bot_button_text_color'], $object)?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo $fields['bot_button_fs']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('bot_button_fs', $fields['bot_button_fs'], $object)?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php echo $fields['bot_button_border_radius']['trans'];?></label>
                        <?php echo erLhcoreClassAbstract::renderInput('bot_button_border_radius', $fields['bot_button_border_radius'], $object)?>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo $fields['bot_button_padding']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('bot_button_padding', $fields['bot_button_padding'], $object)?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label><?php echo $fields['bot_button_padding_left_right']['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput('bot_button_padding_left_right', $fields['bot_button_padding_left_right'], $object)?>
                            </div>
                        </div>
                    </div>

                </div>

                <div role="tabpanel" class="tab-pane" id="customnotification">

                    <div class="form-group">
                        <label><?php echo erLhcoreClassAbstract::renderInput('notification_enabled', $fields['notification_enabled'], $object)?><?php echo $fields['notification_enabled']['trans'];?></label>
                    </div>

                    <div class="form-group">
                        <label><?php echo $fields['ntitle']['trans'];?></label>
                        <?php echo erLhcoreClassAbstract::renderInput('ntitle', $fields['ntitle'], $object)?>
                    </div>
                    
                    <div class="form-group">
                        <label><?php echo $fields['ndomain']['trans'];?></label>
                        <?php echo erLhcoreClassAbstract::renderInput('ndomain', $fields['ndomain'], $object)?>
                    </div>

                    <div class="form-group">
                        <label><?php echo $fields['notification_icon']['trans'];?></label>
                        <?php echo erLhcoreClassAbstract::renderInput('notification_icon', $fields['notification_icon'], $object)?>
                    </div>

                </div>

                <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/custom_tab_content_multiinclude.tpl.php'));?>

        	</div>
        </div>
			  	
	  	<div class="btn-group" role="group" aria-label="...">
			<input type="submit" class="btn btn-secondary" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
			<input type="submit" class="btn btn-secondary" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
			<input type="submit" class="btn btn-secondary" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
		</div>
		
	</div>
	<div class="col-md-4">

        <div class="row">
            <div class="col-3">
                <div id="department-filter">
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'department_id[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => 0,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'ajax'           => 'deps',
                    'list_function_params' => array_merge(['sort' => '`name` ASC','limit' => 20],erLhcoreClassUserDep::conditionalDepartmentFilter()),
                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
                )); ?>
                </div>
            </div>
            <div class="col text-muted">
                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Choose department to test');?>
            </div>
            <div class="col-4">
                <?php if ($object->id != null) : ?>
                    <a href="<?php echo erLhcoreClassDesign::baseurl('theme/export')?>/<?php echo $object->id?>" class="btn mb-2 float-end btn-success btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Download theme')?></a>
                <?php endif;?>
            </div>
        </div>

	<div class="row">
		<div class="col-md-12">
            <iframe id="lhc_iframe" class="border rounded-1" allowtransparency="true" scrolling="no" frameborder="0" src="<?php echo erLhcoreClassDesign::baseurl('chat/demo')?>/(leaveamessage)/true<?php echo $object->id > 0 ? '/(theme)/'.($object->alias != '' ? $object->alias : $object->id) : ''?>" width="320" height="800" style="width: 100%; height: 800px;"></iframe>
			<hr>
		</div>
        <div class="col-md-12">
            <button type="button" class="btn btn-xs btn-info btn-bot">Quick Reply Button</button>
            <hr>
        </div>
	</div>
		<style type="text/css">
        .btn-bot,.btn-bot:hover{
            border-color: #{{bactract_bg_color_bot_button_border}};
            color: #{{bactract_bg_color_bot_button_text_color}};
            background-color: #{{bactract_bg_color_bot_button_background}};
            border-radius: {{ngModelAbstractInput_bot_button_border_radius}}px;
            padding: {{ngModelAbstractInput_bot_button_padding}}px {{ngModelAbstractInput_bot_button_padding_left_right}}px;
            font-size: {{ngModelAbstractInput_bot_button_fs}}px;
        }
        .btn-bot:hover{
            background-color: #{{bactract_bg_color_bot_button_background_hover}};
        }
		</style>

	    <script>	  
	    $('#id_AbstractInput_buble_visitor_background').change(function(){
		    document.getElementById('lhc_iframe').src = document.getElementById('lhc_iframe').src;		    
		});

        $('select[name="AbstractInput_bot_id"]').change(function(){
            $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $(this).val() + '/0/(preview)/1/(asarg)/1', { }, function(data) {
                if (data != '') {
                    $('#trigger-list-id').html(data);
                    renderPreview($('select[name="AbstractInput_trigger_id"]'));
                } else {
                    $('#trigger-list-id').html('<input type="hidden" value="0" name="AbstractInput_trigger_id" />');
                }
            }).fail(function() {

            });
        });

        $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $('select[name="AbstractInput_bot_id"]').val() + '/<?php echo (isset($object->bot_configuration_array['trigger_id'])) ? $object->bot_configuration_array['trigger_id'] : 0 ?>/(preview)/1/(asarg)/1', { }, function(data) {
            if (data != '') {
                $('#trigger-list-id').html(data);
                renderPreview($('select[name="AbstractInput_trigger_id"]'));
            }
        }).fail(function() {

        });

        function renderPreview(inst) {
            $.get(WWW_DIR_JAVASCRIPT + 'theme/renderpreview/' + inst.val(), { }, function(data) {
                $('#trigger-preview-window').html(data);
            }).fail(function() {
                $('#trigger-preview-window').html('');
            });
        }

        function setDefaultNeedHelp() {
            var editor = ace.edit($('#ace-AbstractInput_need_help_html')[0]);
            editor.getSession().setValue(<?php echo json_encode('<div class="container-fluid overflow-auto fade-in p-3 pb-4 {dev_type}" >
<div class="shadow rounded bg-white nh-background">
    <div class="p-2" id="start-chat-btn" style="cursor: pointer">
        <button type="button" id="close-need-help-btn" class="btn-close position-absolute" style="right:30px;top:25px;" aria-label="Close">
          
        </button>
        <div class="d-flex">
          <div class="p-1"><img style="min-width: 50px;" alt="Customer service" class="img-fluid rounded-circle" src="{{need_help_image_url}}"/></div>
          <div class="p-1 flex-grow-1"><h6 class="mb-0">{{need_help_header}}</h6>
            <p class="mb-1" style="font-size: 14px">{{need_help_body}}</p></div>
        </div>
    </div>
</div>
</div>');?>);
        }

        $(function() {
            ace.config.set('basePath', '<?php echo erLhcoreClassDesign::design('js/ace')?>');
            $('textarea[data-editor]').each(function() {
                var textarea = $(this);
                var mode = textarea.data('editor');
                var editDiv = $('<div>', {
                    width: '100%',
                    height: '200px',
                    id: 'ace-'+textarea.attr('name')
                }).insertBefore(textarea);
                textarea.css('display', 'none');
                var editor = ace.edit(editDiv[0]);
                editor.renderer.setShowGutter(true);
                editor.getSession().setValue(textarea.val());
                editor.getSession().setMode("ace/mode/"+mode);
                editor.setOptions({
                    autoScrollEditorIntoView: true,
                    copyWithEmptySelection: true,
                });
                editor.setTheme("ace/theme/github");
                // copy back to textarea on form submit...
                textarea.closest('form').submit(function() {
                    textarea.val(editor.getSession().getValue());
                })
            });

            $('#department-filter .btn-block-department').makeDropdown({"on_delete" : function(){
                    getDepartmentsIds();
                }});

            $('#department-filter').on("change", "input", function(){
                getDepartmentsIds();
            });

            function getDepartmentsIds(){
                let departments = [];
                $('#department-filter input[name^=department_id]').each(function(i){
                    departments.push(this.value);
                });
                document.getElementById('lhc_iframe').src = "<?php echo erLhcoreClassDesign::baseurl('chat/demo')?>/(leaveamessage)/true<?php echo $object->id > 0 ? '/(theme)/'.($object->alias != '' ? $object->alias : $object->id) : ''?>" + (departments.length > 0 ? '/(department)/' + departments.join("/") : '');
                if (sessionStorage) {
                    try {
                        var value = sessionStorage.setItem('lhc_theme_dep', departments.join("/"));
                    } catch(e) {}
                }
            };

            if (sessionStorage) {
                try {
                    var value = sessionStorage.getItem('lhc_theme_dep');
                    if (value) {
                        let dep = value.split("/");
                        dep.forEach(function(item){
                            $('#department-filter input[name^=selector-department_id][value='+item+']').prop('checked', true).trigger("change");
                        });
                    }
                } catch(e) {}
            };

            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        });



        </script>
	</div>
</div>