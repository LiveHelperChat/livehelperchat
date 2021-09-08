<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="mb-2" autocomplete="off">

    <input type="hidden" name="doSearch" value="1">

    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','E-mail');?></label>
                <input type="text" class="form-control form-control-sm" name="email" value="<?php echo htmlspecialchars($input->email)?>" />
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mailbox');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'mailbox_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose mailbox'),
                    'selected_id'    => $input->mailbox_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'mail',
                    'list_function_params' => [],
                    'list_function'  => 'erLhcoreClassModelMailconvMailbox::getList'
                )); ?>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'department_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => $input->department_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => erLhcoreClassUserDep::conditionalDepartmentFilter(),
                    'list_function'  => 'erLhcoreClassModelDepartament::getList'
                )); ?>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department group');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'department_group_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department group'),
                    'selected_id'    => $input->department_group_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => erLhcoreClassUserDep::conditionalDepartmentGroupFilter(),
                    'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList'
                )); ?>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','User');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'user_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
                    'selected_id'    => $input->user_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name_official',
                    'list_function_params' => erLhcoreClassGroupUser::getConditionalUserFilter(),
                    'list_function'  => 'erLhcoreClassModelUser::getUserList'
                )); ?>
            </div>
        </div>

        <?php include(erLhcoreClassDesign::designtpl('lhmailconv/parts/search_attr/user_group.tpl.php')); ?>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from');?></label>
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" class="form-control form-control-sm" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom)?>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?></label>
                <div class="row">
                    <div class="col-md-6">
                        <select name="timefrom_hours" class="form-control form-control-sm">
                            <option value="">Select hour</option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="timefrom_minutes" class="form-control form-control-sm">
                            <option value="">Select minute</option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timefrom_minutes) && $input->timefrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range to');?></label>
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" class="form-control form-control-sm" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input->timeto)?>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?></label>
                <div class="row">
                    <div class="col-md-6">
                        <select name="timeto_hours" class="form-control form-control-sm">
                            <option value="">Select hour</option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="timeto_minutes" class="form-control form-control-sm">
                            <option value="">Select minute</option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Conversation status');?></label>
                <select name="conversation_status" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Any');?></option>
                    <option value="0" <?php if ($input->conversation_status === erLhcoreClassModelMailconvConversation::STATUS_PENDING) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Pending mails');?></option>
                    <option value="1" <?php if ($input->conversation_status === erLhcoreClassModelMailconvConversation::STATUS_ACTIVE) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Active mails');?></option>
                    <option value="2" <?php if ($input->conversation_status === erLhcoreClassModelMailconvConversation::STATUS_CLOSED) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Closed mails');?></option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Subject');?></label>
                <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                    'input_name'     => 'subject_id',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose a subject'),
                    'selected_id'    => $input->subject_id,
                    'css_class'      => 'form-control form-control-sm',
                    'display_name'   => 'name',
                    'list_function_params'  => (new erLhAbstractModelSubject())->getFilter(),
                    'list_function'  => 'erLhAbstractModelSubject::getList'
                )); ?>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Conversation ID');?></label>
                <input type="text" class="form-control form-control-sm" placeholder="<?php echo htmlspecialchars("<id>[,<id>]");?>" name="conversation_id" value="<?php echo htmlspecialchars($input->conversation_id)?>" />
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mail subject');?></label>
                <input type="text" class="form-control form-control-sm" name="subject" value="<?php echo htmlspecialchars($input->subject)?>" />
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Attachment');?></label>
                <select name="has_attachment" class="form-control form-control-sm">
                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Does not matter');?></option>
                    <option value="0" <?php if ($input->has_attachment === erLhcoreClassModelMailconvConversation::ATTACHMENT_EMPTY) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','No attachment');?></option>
                    <option value="1" <?php if ($input->has_attachment === erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Inline');?></option>
                    <option value="2" <?php if ($input->has_attachment === erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Attached');?></option>
                    <option value="3" <?php if ($input->has_attachment === erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Inline or Attached');?></option>
                </select>
            </div>
        </div>

        <?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/search_panel_multiinclude.tpl.php'));?>

    </div>

    <div class="row">
        <div class="col-12">
            <div class="btn-group" role="group" aria-label="...">
                <button class="btn btn-secondary btn-sm" type="submit" name="doSearch"><span class="material-icons">search</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?></button>

                <?php if ($pages->items_total > 0) : $appendPrintExportURL = '';?>
                <?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/search_panel_append_print_multiinclude.tpl.php'));?>
                <button type="button" onclick="return lhc.revealModal({'title' : 'Export', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(xls)/1?<?php echo $appendPrintExportURL?>'})" class="btn btn-secondary btn-sm"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export')?> (<?php echo $pages->items_total?>)</button>
                <?php endif; ?>

                <a class="btn btn-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/sendemail')?>">
                    <i class="material-icons">email</i>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','New e-mail')?>
                </a>
            </div>
        </div>
    </div>

</form>

<script>
    $(function() {
        $('#id_timefrom,#id_timeto').fdatepicker({
            format: 'yyyy-mm-dd'
        });
        $('.btn-block-department').makeDropdown();
    });
</script>