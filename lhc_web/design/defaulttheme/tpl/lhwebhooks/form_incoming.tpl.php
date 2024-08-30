<div class="form-group" ng-non-bindable>
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Name');?></label>
    <input type="text" class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item->name);?>" />
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group" ng-non-bindable>
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Identifier');?></label>
            <input type="text" class="form-control form-control-sm" name="identifier" id="id_identifier" onkeyup="$('#api-incoming-url').val($('#api-incoming-url').attr('data-base')+$('#LocaleID').val()+$('#api-incoming-url').attr('data-url')+$(this).val())" value="<?php echo htmlspecialchars($item->identifier);?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group" ng-non-bindable>
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Scope. This should be the same for all same provider implementations.');?></label>
            <input type="text" class="form-control form-control-sm" name="scope" value="<?php echo htmlspecialchars($item->scope);?>" />
        </div>
    </div>
</div>



<div class="mb-2">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','URL to put in third party Rest API service. Append ?output=json for JSON format output.');?></label>
    <div class="input-group">
        <select onchange="$('#api-incoming-url').val($('#api-incoming-url').attr('data-base')+$('#LocaleID').val()+$('#api-incoming-url').attr('data-url')+$('#id_identifier').val())" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/htmlcode','Choose a language');?>" id="LocaleID" class="form-select form-select-sm w-25">
            <?php foreach (erConfigClassLhConfig::getInstance()->getSetting( 'site', 'available_site_access' ) as $locale ) : ?>
                <option value="/<?php echo $locale?>"><?php echo $locale?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" ng-non-bindable class="form-control form-control-sm w-75" id="api-incoming-url" data-base="<?php echo erLhcoreClassSystem::getHost()?>" data-url="<?php echo erLhcoreClassDesign::baseurldirect('webhooks/incoming')?>/" value="<?php echo erLhcoreClassSystem::getHost()?><?php echo erLhcoreClassDesign::baseurldirect('webhooks/incoming')?>/<?php echo htmlspecialchars($item->identifier);?>">
    </div>
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group" ng-non-bindable>
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Icon or image path. E.g.');?> <span class="badge bg-secondary">flags/lt.png</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','images prefix is not needed.');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="E.g sms" name="icon" value="<?php echo htmlspecialchars($item->icon);?>" />
        </div>
    </div>
    <div class="col-6">
        <div class="form-group" ng-non-bindable>
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Icon color');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="E.g green, #2bd4a8" name="icon_color" value="<?php echo htmlspecialchars($item->icon_color);?>" />
        </div>
    </div>
</div>


<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
    <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
        'input_name'     => 'dep_id',
        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
        'selected_id'    => $item->dep_id,
        'css_class'      => 'form-control form-control-sm',
        'display_name'   => 'name',
        'list_function_params' => array('limit' => false, 'sort' => '`name` ASC'),
        'list_function'  => 'erLhcoreClassModelDepartament::getList'
    )); ?>
</div>

<div class="form-group">
    <label><input type="checkbox" value="on" name="disabled" <?php echo $item->disabled == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Disabled')?></label>
</div>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><input type="checkbox" value="on" name="log_incoming" <?php echo $item->log_incoming == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Log request. All request will be logged')?></label>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><input type="checkbox" value="on" name="log_failed_parse" <?php echo $item->log_failed_parse == 1 ? 'checked="checked"' : '' ?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/new','Log exceptions. Only failed parse requests will be logged.')?></label>
        </div>
    </div>
</div>

<div class="form-group">
    <label><input type="checkbox" ng-model="show_wh_integration"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Show integration information.');?></label>
</div>

<ul>
    <li>Is the fileinfo extension detected - <?php if (extension_loaded ('fileinfo' )) : ?><span class="badge bg-success"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Yes')?></span> <?php else : ?> <span class="badge bg-danger"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','No, uploaded files types might not be detected correctly!')?></span><?php endif; ?>
</ul>

<div ng-show="show_wh_integration">

<ul class="nav nav-tabs mb-3" role="tablist">
    <li role="presentation" class="nav-item"><a href="#main_message_attributes" class="nav-link active" aria-controls="main_message_attributes" role="tab" data-bs-toggle="tab" aria-selected="false"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Main attributes')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#text_messages" aria-controls="text_messages" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Text messages 1')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#text_messages_2" aria-controls="text_messages_2" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Text messages 2')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#text_messages_3" aria-controls="text_messages_3" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Text messages 3')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#text_messages_4" aria-controls="text_messages_4" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Text messages 4')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#button_payload_1" aria-controls="button_payload_1" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Button payload 1')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#button_payload_2" aria-controls="button_payload_2" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Button payload 2')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#button_payload_3" aria-controls="button_payload_3" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Button payload 3')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#attachments" aria-controls="attachments" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attachments')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#img-attachments" aria-controls="img-attachments" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Images/Video 1')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#img-attachments_2" aria-controls="img-attachments_2" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Images/Video 2')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#img-attachments_3" aria-controls="img-attachments_3" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Images/Video 3')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#img-attachments_4" aria-controls="img-attachments_4" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Images/Video 4')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#img-attachments_5" aria-controls="img-attachments_5" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Images/Video 5')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#img-attachments_6" aria-controls="img-attachments_6" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Images/Video 6')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#msg-delivery-status" aria-controls="msg-delivery-status" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Messages delivery and reactions')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#chat_options" aria-controls="chat_options" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat options')?></a></li>
    <li role="presentation" class="nav-item"><a class="nav-link" href="#wh_attributes" aria-controls="wh_attributes" role="tab" data-bs-toggle="tab" aria-selected="true"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attributes')?></a></li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane form-group active" id="main_message_attributes">

        <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message related attributes')?></h4>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Messages attribute location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.messages" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                       <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.message_direct" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','This attribute contains direct message and NOT a messages array');?></label>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Messages ID');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.message_id" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Reply to message ID');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.message_id_reply" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Time');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.time" value="" />
                </div>
            </div>
        </div>

        <hr>
        <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat related attributes')?></h4>
        <div class="row">
            <div class="col-6">

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Nick');?></label>
                            <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.nick" value="" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Nick preg match rule');?></label>
                            <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.nick_pregmatch" placeholder="/(?!^\d+$)^.+$/is" value="" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Phone');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.phone" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','IP');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.ip" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Country, 2 letters code');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.country_code" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','E-mail');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.email" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID field location');?></label>
                            <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.chat_id" value="" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID 2 field location');?></label>
                            <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.chat_id_2" value="" />
                        </div>
                    </div>

                    <div class="col-12">
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Switch Chat ID with Chat ID 2 if this condition matches');?>
                        <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.chat_id_switch" value="" />
                    </div>


                    <div class="col-12">
                        <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','We will combine both fields into single identifier');?>&nbsp;<span class="badge bg-secondary">chat_id__chat_id_2</span>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID Template for manual message sending. Use {chat_id} as placeholder');?></label>
                    <input type="text" class="form-control form-control-sm" title="{chat_id}@c.us" placeholder="E.g {chat_id}@c.us" ng-model="webhookincomingsctl.conditions.chat_id_template" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID field replace rule');?></label>
                            <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.chat_id_preg_rule" value="" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID field replace value');?></label>
                            <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.chat_id_preg_value" value="" />
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','General conditions for messages being processed. These are first level attributes');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.main_cond" placeholder="main attribute=value expected||main attribute=value expected" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Additional field location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.add_field_value" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Invisible additional field');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.add_field_2_value" value="" />
                </div>
            </div>
        </div>

    </div>

    <div role="tabpanel" class="tab-pane form-group" id="text_messages">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_body"></textarea>
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered text message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond" value="" />
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered bot/operator messages should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_op" value="" />
        </div>
    </div>

    <div role="tabpanel" class="tab-pane form-group" id="text_messages_2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_body_2"></textarea>
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered text message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_2" value="" />
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered bot/operator messages should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_op_2" value="" />
        </div>
    </div>

    <div role="tabpanel" class="tab-pane form-group" id="text_messages_3">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_body_3"></textarea>
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered text message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_3" value="" />
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered bot/operator messages should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_op_3" value="" />
        </div>
    </div>

    <div role="tabpanel" class="tab-pane form-group" id="text_messages_4">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_body_4"></textarea>
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered text message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_4" value="" />
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered bot/operator messages should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_op_4" value="" />
        </div>
    </div>

    <div role="tabpanel" class="tab-pane form-group" id="attachments">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_attachments"></textarea>
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_attachments_download" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Download attachment instead of using external URL');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Image body attributes. URL or base64 encoded content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_attachments_body" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file name. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_name" ng-model="webhookincomingsctl.conditions.msg_cond_attachments_file_name" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file size. File size check will apply if defined. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_size" ng-model="webhookincomingsctl.conditions.msg_cond_attachments_file_size" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds mime type. Optional.');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g type" ng-model="webhookincomingsctl.conditions.msg_cond_attachments_mime_type" value="" />
        </div>
        
        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Decode file options');?> </h5>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','URL To make request to get content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_attachments_url_decode" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content');?></label>
            <textarea class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_attachments_url_decode_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content headers');?></label>
            <textarea class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_attachments_url_headers_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Response location');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_attachments_url_decode_output" value="" />
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_cond_attachments_url_remote_location" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Returned response is location to download file');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Remote request additional headers');?></label>
            <textarea placeholder="Authorization: Bearer {{'{'+'{msg.incoming_webhook.attributes.access_token}'+'}'}}" class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_attachments_url_remote_headers_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered attachment message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_attachments" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered bot/operator messages should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_attachments_op" value="" />
        </div>
    </div>

    <div role="tabpanel" class="tab-pane form-group" id="button_payload_1">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_btn_body_1"></textarea>
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Button payload location');?></label>
            <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_btn_payload_1" value="" />
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered button payload - message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_btn_cond_1" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','AND Message text has to start with');?> E.g bpayload__,trigger__,mycustompayload__</label>
            <input type="text" class="form-control form-control-sm" placeholder="bpayload__,trigger__,mycustompayload__" ng-model="webhookincomingsctl.conditions.msg_btn_cond_payload_1" value="" />
        </div>

    </div>

    <div role="tabpanel" class="tab-pane form-group" id="button_payload_2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_btn_body_2"></textarea>
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Button payload location');?></label>
            <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_btn_payload_2" value="" />
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered button payload - message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_btn_cond_2" value="" />
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','AND Message text has to start with');?> E.g bpayload__,trigger__,mycustompayload__</label>
            <input type="text" class="form-control form-control-sm" placeholder="bpayload__,trigger__,mycustompayload__" ng-model="webhookincomingsctl.conditions.msg_btn_cond_payload_2" value="" />
        </div>
    </div>

    <div role="tabpanel" class="tab-pane form-group" id="button_payload_3">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_btn_body_3"></textarea>
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Button payload location');?></label>
            <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_btn_payload_3" value="" />
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered button payload - message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_btn_cond_3" value="" />
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','AND Message text has to start with');?> E.g bpayload__,trigger__,mycustompayload__</label>
            <input type="text" class="form-control form-control-sm" placeholder="bpayload__,trigger__,mycustompayload__" ng-model="webhookincomingsctl.conditions.msg_btn_cond_payload_3" value="" />
        </div>
    </div>

    <div role="tabpanel" class="tab-pane form-group" id="img-attachments">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_img"></textarea>
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_img_download" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Download image instead of using external URL');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Image body attributes. URL or base64 encoded content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_body" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file name. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_name" ng-model="webhookincomingsctl.conditions.msg_cond_img_file_name" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file size. File size check will apply if defined. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_size" ng-model="webhookincomingsctl.conditions.msg_cond_img_file_size" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds mime type. Optional.');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g type" ng-model="webhookincomingsctl.conditions.msg_cond_img_mime_type" value="" />
        </div>

        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Decode file options');?> </h5>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','URL To make request to get content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_url_decode" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content');?></label>
            <textarea class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_url_decode_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content headers');?></label>
            <textarea placeholder="Authorization: Bearer {{'{'+'{msg.incoming_webhook.attributes.access_token}'+'}'}}" class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_url_headers_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Response location');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_url_decode_output" value="" />
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_cond_img_url_remote_location" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Returned response is location to download image');?></label>
        </div>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Remote request additional headers');?></label>
            <textarea placeholder="Authorization: Bearer {{'{'+'{msg.incoming_webhook.attributes.access_token}'+'}'}}" class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_url_remote_headers_content"></textarea>
        </div>

        <hr>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered images message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_img" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered bot/operator messages should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_img_op" value="" />
        </div>

    </div>

    <div role="tabpanel" class="tab-pane form-group" id="img-attachments_2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_img_2"></textarea>
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_img_2_download" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Download image instead of using external URL');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Image body attributes. URL or base64 encoded content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_2_body" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file name. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_name" ng-model="webhookincomingsctl.conditions.msg_cond_img_2_file_name" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file size. File size check will apply if defined. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_size" ng-model="webhookincomingsctl.conditions.msg_cond_img_2_file_size" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds mime type. Optional.');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g type" ng-model="webhookincomingsctl.conditions.msg_cond_img_2_mime_type" value="" />
        </div>

        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Decode file options');?> </h5>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','URL To make request to get content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_2_url_decode" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content');?></label>
            <textarea class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_2_url_decode_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content headers');?></label>
            <textarea placeholder="Authorization: Bearer {{'{'+'{msg.incoming_webhook.attributes.access_token}'+'}'}}" class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_2_url_headers_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Response location');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_2_url_decode_output" value="" />
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_cond_img_2_url_remote_location" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Returned response is location to download image');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Remote request additional headers');?></label>
            <textarea placeholder="Authorization: Bearer {{'{'+'{msg.incoming_webhook.attributes.access_token}'+'}'}}" class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_2_url_remote_headers_content"></textarea>
        </div>

        <hr>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered images message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_img_2" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered bot/operator messages should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_img_2_op" value="" />
        </div>

    </div>
    <div role="tabpanel" class="tab-pane form-group" id="img-attachments_3">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_img_3"></textarea>
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_img_3_download" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Download image instead of using external URL');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Image body attributes. URL or base64 encoded content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_3_body" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file name. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_name" ng-model="webhookincomingsctl.conditions.msg_cond_img_3_file_name" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file size. File size check will apply if defined. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_size" ng-model="webhookincomingsctl.conditions.msg_cond_img_3_file_size" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds mime type. Optional.');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g type" ng-model="webhookincomingsctl.conditions.msg_cond_img_3_mime_type" value="" />
        </div>

        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Decode file options');?> </h5>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','URL To make request to get content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_3_url_decode" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content');?></label>
            <textarea class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_3_url_decode_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content headers');?></label>
            <textarea placeholder="Authorization: Bearer {{'{'+'{msg.incoming_webhook.attributes.access_token}'+'}'}}" class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_3_url_headers_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Response location');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_3_url_decode_output" value="" />
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_cond_img_3_url_remote_location" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Returned response is location to download image');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Remote request additional headers');?></label>
            <textarea placeholder="Authorization: Bearer {{'{'+'{msg.incoming_webhook.attributes.access_token}'+'}'}}" class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_3_url_remote_headers_content"></textarea>
        </div>

        <hr>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered images message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_img_3" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered bot/operator messages should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_img_3_op" value="" />
        </div>

    </div>

    <div role="tabpanel" class="tab-pane form-group" id="img-attachments_4">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_img_4"></textarea>
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_img_4_download" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Download image instead of using external URL');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Image body attributes. URL or base64 encoded content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_4_body" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file name. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_name" ng-model="webhookincomingsctl.conditions.msg_cond_img_4_file_name" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file size. File size check will apply if defined. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_size" ng-model="webhookincomingsctl.conditions.msg_cond_img_4_file_size" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds mime type. Optional.');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g type" ng-model="webhookincomingsctl.conditions.msg_cond_img_4_mime_type" value="" />
        </div>

        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Decode file options');?> </h5>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','URL To make request to get content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_4_url_decode" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content');?></label>
            <textarea class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_4_url_decode_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content headers');?></label>
            <textarea placeholder="Authorization: Bearer {{'{'+'{msg.incoming_webhook.attributes.access_token}'+'}'}}" class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_4_url_headers_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Response location');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_4_url_decode_output" value="" />
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_cond_img_4_url_remote_location" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Returned response is location to download image');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Remote request additional headers');?></label>
            <textarea placeholder="Authorization: Bearer {{'{'+'{msg.incoming_webhook.attributes.access_token}'+'}'}}" class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_4_url_remote_headers_content"></textarea>
        </div>

        <hr>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered images message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_img_4" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered bot/operator messages should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_img_4_op" value="" />
        </div>

    </div>

    <div role="tabpanel" class="tab-pane form-group" id="img-attachments_5">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_img_5"></textarea>
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_img_5_download" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Download image instead of using external URL');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Image body attributes. URL or base64 encoded content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_5_body" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file name. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_name" ng-model="webhookincomingsctl.conditions.msg_cond_img_5_file_name" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file size. File size check will apply if defined. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_size" ng-model="webhookincomingsctl.conditions.msg_cond_img_5_file_size" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds mime type. Optional.');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g type" ng-model="webhookincomingsctl.conditions.msg_cond_img_5_mime_type" value="" />
        </div>

        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Decode file options');?> </h5>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','URL To make request to get content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_5_url_decode" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content');?></label>
            <textarea class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_5_url_decode_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content headers');?></label>
            <textarea placeholder="Authorization: Bearer {{'{'+'{msg.incoming_webhook.attributes.access_token}'+'}'}}" class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_5_url_headers_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Response location');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_5_url_decode_output" value="" />
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_cond_img_5_url_remote_location" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Returned response is location to download image');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Remote request additional headers');?></label>
            <textarea placeholder="Authorization: Bearer {{'{'+'{msg.incoming_webhook.attributes.access_token}'+'}'}}" class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_5_url_remote_headers_content"></textarea>
        </div>

        <hr>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered images message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_img_5" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered bot/operator messages should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_img_5_op" value="" />
        </div>

    </div>

    <div role="tabpanel" class="tab-pane form-group" id="img-attachments_6">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message body content');?></label>
            <textarea class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_img_6"></textarea>
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_img_6_download" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Download image instead of using external URL');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Image body attributes. URL or base64 encoded content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_6_body" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file name. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_name" ng-model="webhookincomingsctl.conditions.msg_cond_img_6_file_name" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds file size. File size check will apply if defined. Optional');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g file_size" ng-model="webhookincomingsctl.conditions.msg_cond_img_6_file_size" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Attribute which holds mime type. Optional.');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute. E.g type" ng-model="webhookincomingsctl.conditions.msg_cond_img_6_mime_type" value="" />
        </div>

        <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Decode file options');?> </h5>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','URL To make request to get content');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_6_url_decode" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content');?></label>
            <textarea class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_6_url_decode_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Request content headers');?></label>
            <textarea placeholder="Authorization: Bearer {{'{'+'{msg.incoming_webhook.attributes.access_token}'+'}'}}" class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_6_url_headers_content"></textarea>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Response location');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="Please provide attribute" ng-model="webhookincomingsctl.conditions.msg_cond_img_6_url_decode_output" value="" />
        </div>

        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_cond_img_6_url_remote_location" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Returned response is location to download image');?></label>
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Remote request additional headers');?></label>
            <textarea placeholder="Authorization: Bearer {{'{'+'{msg.incoming_webhook.attributes.access_token}'+'}'}}" class="form-control" ng-model="webhookincomingsctl.conditions.msg_cond_img_6_url_remote_headers_content"></textarea>
        </div>

        <hr>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered images message should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_img_6" value="" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered bot/operator messages should have attribute value equal to');?></label>
            <input type="text" class="form-control form-control-sm" placeholder="message_attribute=value expected||message_attribute=value expected" ng-model="webhookincomingsctl.conditions.msg_cond_img_6_op" value="" />
        </div>

    </div>

    <div role="tabpanel" class="tab-pane form-group" id="msg-delivery-status">

        <h5>Pending</h5>
        <p>This is default status and no conditions are needed</p>

        <h5>Sent</h5>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message ID location');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="" ng-model="webhookincomingsctl.conditions.msg_delivery_sent_id" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered text message should have attribute value equal to');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="message.attribute.location=accepted,processing,sent" ng-model="webhookincomingsctl.conditions.msg_delivery_sent_condition" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID field location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_delivery_sent_chat_id" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID 2 field location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_delivery_sent_chat_id_2" value="" />
                </div>
            </div>
        </div>

        <h5>Delivered</h5>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message ID location');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="" ng-model="webhookincomingsctl.conditions.msg_delivery_delivered_id" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered text message should have attribute value equal to');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="message.attribute.location=delivered" ng-model="webhookincomingsctl.conditions.msg_delivery_delivered_condition" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID field location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_delivery_delivered_chat_id" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID 2 field location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_delivery_delivered_chat_id_2" value="" />
                </div>
            </div>
        </div>

        <h5>Read</h5>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message ID location');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="" ng-model="webhookincomingsctl.conditions.msg_delivery_read_id" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered text message should have attribute value equal to');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="message.attribute.location=read" ng-model="webhookincomingsctl.conditions.msg_delivery_read_condition" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID field location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_delivery_read_chat_id" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID 2 field location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_delivery_read_chat_id_2" value="" />
                </div>
            </div>
        </div>

        <h5>Rejected</h5>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message ID location');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="" ng-model="webhookincomingsctl.conditions.msg_delivery_rejected_id" value="" />
                </div>
            </div>
           <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered text message should have attribute value equal to');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="message.attribute.location=rejected" ng-model="webhookincomingsctl.conditions.msg_delivery_rejected_condition" value="" />
                </div>
           </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID field location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_delivery_rejected_chat_id" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID 2 field location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_delivery_rejected_chat_id_2" value="" />
                </div>
            </div>
        </div>

        <hr>

        <h5>React action</h5>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message id location to which visitor reacted');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="" ng-model="webhookincomingsctl.conditions.msg_delivery_reaction_id" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Reaction message ID');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="" ng-model="webhookincomingsctl.conditions.msg_delivery_reaction_action_id" value="" />
                </div>
            </div>
           <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered reaction message');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="type=reaction" ng-model="webhookincomingsctl.conditions.msg_delivery_reaction_condition" value="" />
                </div>
           </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Reaction emoji location');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="type=reaction" ng-model="webhookincomingsctl.conditions.msg_delivery_reaction_location" value="" />
                </div>
           </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID field location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_delivery_reaction_chat_id" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID 2 field location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_delivery_reaction_chat_id_2" value="" />
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_delivery_reaction_use_msg_id" value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Try to find chat by message id if we chat was not found by id');?></label>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_delivery_reaction_use_emoji" value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Reaction is a standalone unicode character');?></label>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_delivery_reaction_remove_prev" value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Remove previous visitor reaction on action');?></label>
                </div>
            </div>
        </div>

        <h5>Un-react action</h5>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Message id location to which visitor reacted');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="" ng-model="webhookincomingsctl.conditions.msg_delivery_un_reaction_id" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Reaction message ID');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="" ng-model="webhookincomingsctl.conditions.msg_delivery_un_reaction_action_id" value="" />
                </div>
            </div>
           <div class="col-12">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','For message being considered reaction message');?></label>
                    <input type="text" class="form-control form-control-sm" placeholder="type=reaction" ng-model="webhookincomingsctl.conditions.msg_delivery_un_reaction_condition" value="" />
                </div>
           </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID field location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_delivery_un_reaction_chat_id" value="" />
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat ID 2 field location');?></label>
                    <input type="text" class="form-control form-control-sm" ng-model="webhookincomingsctl.conditions.msg_delivery_un_reaction_chat_id_2" value="" />
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_delivery_un_reaction_use_msg_id" value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Try to find chat by message id if we chat was not found by id');?></label>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.msg_delivery_un_reaction_use_emoji" value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Reaction is a standalone unicode character');?></label>
                </div>
            </div>
        </div>



    </div>


    <div role="tabpanel" class="tab-pane form-group" id="chat_options">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','If previous chat is found and it is closed we should');?></label>
            <select ng-model="webhookincomingsctl.conditions.chat_status" class="form-control form-control-sm">
                <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Create a new chat.');?></option>
                <option value="pending"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Set previous chat to Pending/Bot depending on department configuration.');?></option>
                <option value="active"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Set as active if operator was assigned. Operator will not be reset.');?></option>
            </select>
        </div>
        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.reset_op" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Operator should be reset if chat being transferred to pending state.');?></label>
        </div>
        <div class="form-group">
            <label><input type="checkbox" ng-model="webhookincomingsctl.conditions.reset_dep" value="on" > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Chat should be reset to default department. Bot also will be set to default.');?></label>
        </div>
    </div>

    <div role="tabpanel" class="tab-pane form-group" id="wh_attributes">
        <p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','These attributes can be accessed in Rest API. So you would not need to define them there.');?></p>

        <button type="button" class="btn btn-secondary btn-sm" ng-click="webhookincomingsctl.addParam(webhookincomingsctl.conditions.attr)">Add parameter</button>

        <div ng-repeat="paramQuery in webhookincomingsctl.conditions.attr" class="mt-2">
            <div class="row">
                <div class="col-4">
                    <input type="text" class="form-control form-control-sm" ng-model="paramQuery.key" placeholder="Key">
                </div>
                <div class="col-4">
                    <input type="text" class="form-control form-control-sm" ng-model="paramQuery.value" placeholder="Value">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger d-block w-100 btn-xs" ng-click="webhookincomingsctl.deleteParam(webhookincomingsctl.conditions.attr,paramQuery)">-</button>
                </div>
            </div>
        </div>

    </div>

</div>

<hr/>

<h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Expected JSON payload for text message.');?></h5>

<pre>
<?php echo '{'."\n".'
    {{webhookincomingsctl.conditions.messages ? "\""+webhookincomingsctl.conditions.messages+"\":[" : ""}}'."\n".'
        "{{webhookincomingsctl.conditions.msg_body}}" : "'. erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','expected message body') .'",'."\n".'
        "{{webhookincomingsctl.conditions.nick}}": "'.  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','expected nick').'",'."\n".'
        "{{webhookincomingsctl.conditions.time}}": "'.  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','unix timestamp E.g 1504208593').'",'."\n".'
        "{{webhookincomingsctl.conditions.chat_id}}": "'.  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/webhooks','Unique Chat ID.').'",'."\n".'
        {{webhookincomingsctl.conditions.msg_cond ? "\""+webhookincomingsctl.conditions.msg_cond.replaceAll("=","\":\"").replaceAll("||","\",        \n\"")+"\"," : ""}}'."\n".'
    {{webhookincomingsctl.conditions.messages ? "]" : ""}}'."\n".'
    {{webhookincomingsctl.conditions.main_cond ? "\""+webhookincomingsctl.conditions.main_cond.replaceAll("=","\":\"").replaceAll("||","\",        \n\"")+"\"," : ""}}
'."\n".'}'?>
</pre>

</div>

<textarea name="configuration" class="hide" ng-model="webhookincomingsctl.conditions_json"></textarea>