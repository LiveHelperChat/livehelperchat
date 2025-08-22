<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight" class="mb-2" autocomplete="off">

    <input type="hidden" name="doSearch" value="1">

    <div class="row">
        <div class="col-md-1">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','ID');?></label>
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control form-control-sm" placeholder="<?php echo htmlspecialchars("<id>[,<id>]");?>" name="conversation_id" value="<?php echo htmlspecialchars($input->conversation_id)?>" />
                    <button class="btn dropdown-toggle btn-outline-secondary border-secondary-control" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="material-icons">settings</span>
                    </button>
                    <div class="dropdown-menu">
                        <label class="dropdown-item mb-0 ps-2">
                            <input type="radio" <?php if (!isset($input->search_id) || $input->search_id == 1) : ?>checked="checked"<?php endif; ?> name="search_id" value="1"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Conversation');?>
                        </label>
                        <label class="dropdown-item mb-0 ps-2">
                            <input type="radio" <?php if (isset($input->search_id) && $input->search_id == 2) : ?>checked="checked"<?php endif; ?> name="search_id" value="2"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Message');?>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label><?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/email_title.tpl.php')); ?></label>
                <input type="text" class="form-control form-control-sm" name="email" value="<?php echo htmlspecialchars($input->email)?>" />
            </div>
        </div>

        <div class="col-md-1">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mail subject');?></label>
                <input type="text" class="form-control form-control-sm" name="subject" value="<?php echo htmlspecialchars($input->subject)?>" />
            </div>
        </div>

        <div class="col-md-1">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mailbox');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'mailbox_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose mailbox'),
                    'selected_id'    => $input->mailbox_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'mail',
                    'list_function_params' => ['limit' => false, 'sort' => '`mail` ASC'],
                    'list_function'  => 'erLhcoreClassModelMailconvMailbox::getList'
                )); ?>
            </div>
        </div>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/user_title.tpl.php')); ?>
        <div class="col-md-1">
            <div class="form-group">
                <label><?php echo $userTitle['user'];?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'user_ids[]',
                    'optional_field' => $userTitle['user_select'],
                    'selected_id'    => $input->user_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name_official',
                    'ajax'           => 'users',
                    'list_function_params' =>  array_merge(erLhcoreClassGroupUser::getConditionalUserFilter(),array('sort' => '`name` ASC','limit' => 50)),
                    'list_function'  => 'erLhcoreClassModelUser::getUserList'
                )); ?>
            </div>
        </div>

        <div class="col-md-1">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Status');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'conversation_status_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose status'),
                    'selected_id'    => $input->conversation_status_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => array(),
                    'list_function'  => function () {
                        $items = array();

                        $item = new StdClass();
                        $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','New mails');
                        $item->id = erLhcoreClassModelMailconvConversation::STATUS_PENDING;
                        $items[] = $item;

                        $item = new StdClass();
                        $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Active mails');
                        $item->id = erLhcoreClassModelMailconvConversation::STATUS_ACTIVE;
                        $items[] = $item;

                        $item = new StdClass();
                        $item->name = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Closed mails');
                        $item->id = erLhcoreClassModelMailconvConversation::STATUS_CLOSED;
                        $items[] = $item;

                        return $items;
                    }
                )); ?>
            </div>
        </div>

        <?php include(erLhcoreClassDesign::designtpl('lhmailconv/parts/search_attr/user_group.tpl.php')); ?>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Department');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'department_ids[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose department'),
                    'selected_id'    => $input->department_ids,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'ajax'           => 'deps',
                    'list_function_params' => array_merge(['sort' => '`name` ASC', 'limit' => 20],erLhcoreClassUserDep::conditionalDepartmentFilter()),
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
                    'list_function_params' => array_merge(['sort' => '`name` ASC', 'limit' => false],erLhcoreClassUserDep::conditionalDepartmentGroupFilter()),
                    'list_function'  => 'erLhcoreClassModelDepartamentGroup::getList'
                )); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 pb-2">
            <a href="#" onclick="$('#advanced-search').toggle()"><span class="material-icons">search</span>&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Advanced search');?></a>
        </div>
        <div class="col-12" id="advanced-search" style="display: none">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from');?>
                            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/date_picker_range.tpl.php')); ?>
                        </label>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" class="form-control form-control-sm" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom)?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?></label>
                        <div class="row">
                            <div class="col-md-4">
                                <select name="timefrom_hours" class="form-control form-control-sm">
                                    <option value="">Select hour</option>
                                    <?php for ($i = 0; $i <= 23; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="timefrom_minutes" class="form-control form-control-sm">
                                    <option value="">Select minute</option>
                                    <?php for ($i = 0; $i <= 59; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timefrom_minutes) && $input->timefrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="timefrom_seconds" class="form-control form-control-sm">
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                                    <?php for ($i = 0; $i <= 59; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timefrom_seconds) && $input->timefrom_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
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

                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?></label>
                        <div class="row">
                            <div class="col-md-4">
                                <select name="timeto_hours" class="form-control form-control-sm">
                                    <option value="">Select hour</option>
                                    <?php for ($i = 0; $i <= 23; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="timeto_minutes" class="form-control form-control-sm">
                                    <option value="">Select minute</option>
                                    <?php for ($i = 0; $i <= 59; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="timeto_seconds" class="form-control form-control-sm">
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                                    <?php for ($i = 0; $i <= 59; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timeto_seconds) && $input->timeto_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Sender');?></label>
                        <select name="is_external" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Any');?></option>
                            <option value="0" <?php if ($input->is_external === 0) : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','We');?></option>
                            <option value="1" <?php if ($input->is_external === 1) : ?>selected="selected"<?php endif; ?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Visitor');?></option>
                        </select>
                    </div>
                </div>

                <div class="col-12">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Subject')?></label>
                                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                                    'input_name'     => 'subject_id[]',
                                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select subject'),
                                    'selected_id'    => $input->subject_id,
                                    'css_class'      => 'form-control',
                                    'display_name'   => 'name',
                                    'list_function'  => 'erLhAbstractModelSubject::getList',
                                    'list_function_params'  => array('limit' => false)
                                )); ?>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Attachment');?></label>
                                <select name="has_attachment" class="form-control form-control-sm">
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Does not matter');?></option>
                                    <option value="1" <?php if ($input->has_attachment === erLhcoreClassModelMailconvConversation::ATTACHMENT_INLINE) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Inline');?></option>
                                    <option value="2" <?php if ($input->has_attachment === erLhcoreClassModelMailconvConversation::ATTACHMENT_FILE) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','As file');?></option>
                                    <option value="3" <?php if ($input->has_attachment === erLhcoreClassModelMailconvConversation::ATTACHMENT_MIX) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Inline or as file');?></option>
                                    <option value="5" <?php if ($input->has_attachment === 5) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','No attachment (inline)');?></option>
                                    <option value="4" <?php if ($input->has_attachment === 4) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','No attachment (as file)');?></option>
                                    <option value="0" <?php if ($input->has_attachment === erLhcoreClassModelMailconvConversation::ATTACHMENT_EMPTY) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','No attachment (inline or as file)');?></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Language');?></label>
                                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                                    'input_name'     => 'lang_ids[]',
                                    'attr_id'        => 'short_code',
                                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose a language'),
                                    'selected_id'    => $input->lang_ids,
                                    'css_class'      => 'form-control',
                                    'display_name'   => function($item) {
                                        return '[' . $item->short_code . '] '.$item->lang_name;
                                    },
                                    'list_function_params' => ['limit' => false, 'sort' => '`lang_name` ASC','filternot' => ['short_code' => '']],
                                    'list_function'  => 'erLhcoreClassModelSpeechLanguageDialect::getList'
                                )); ?>
                            </div>
                        </div>


                        <div class="col-md-2">
                            <label>Sort by</label>
                            <select name="sortby" class="form-control form-control-sm">
                                <option <?php if ($input->sortby == 'iddesc'|| $input->sortby == '') : ?>selected="selected"<?php endif; ?> value="iddesc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Newest first (default)');?></option>
                                <option <?php if ($input->sortby == 'idasc') : ?>selected="selected"<?php endif; ?> value="idasc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Oldest first');?></option>
                                <option <?php if ($input->sortby == 'highprioritynew') : ?>selected="selected"<?php endif; ?> value="highprioritynew"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Higher priority, newest first');?></option>
                                <option <?php if ($input->sortby == 'lowpriorityold') : ?>selected="selected"<?php endif; ?> value="lowpriorityold"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Higher priority, oldest first');?></option>
                                <option <?php if ($input->sortby == 'statuspriority') : ?>selected="selected"<?php endif; ?> value="statuspriority"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Active, New sorted by higher priority');?></option>
                                <option <?php if ($input->sortby == 'lastupdatedesc') : ?>selected="selected"<?php endif; ?> value="lastupdatedesc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Newest replies first');?></option>
                                <option <?php if ($input->sortby == 'lastupdateasc') : ?>selected="selected"<?php endif; ?> value="lastupdateasc"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('pagelayout/pagelayout','Oldest replies first');?></option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Phone');?></label>
                                <input type="text" class="form-control form-control-sm" name="phone" value="<?php echo htmlspecialchars($input->phone)?>" />
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group mb-0">
                                <label class="col-form-label"><input type="checkbox" name="undelivered" <?php $input->undelivered == 1 ? print ' checked="checked" ' : ''?> value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Undelivered')?></label>
                            </div>
                            <div class="form-group mb-0">
                                <label class="col-form-label"><input type="checkbox" name="is_followup" <?php $input->is_followup == 1 ? print ' checked="checked" ' : ''?> value="on" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Is followup')?></label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Open status');?></label>
                            <select name="opened" class="form-control form-control-sm">
                                <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Any')?></option>
                                <option value="0" <?php if ($input->opened === 0) : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Not opened')?></option>
                                <option value="1" <?php if ($input->opened === 1) : ?>selected="selected"<?php endif;?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Opened')?></option>
                            </select>
                        </div>
                        <?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/search_panel_multiinclude.tpl.php'));?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="btn-group" role="group" aria-label="...">
                <button class="btn btn-primary btn-sm" type="submit" name="doSearch"><span class="material-icons">search</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?></button>

                <select name="ipp" class="form-control-sm rounded-0" onchange="this.form.submit()">
                    <option value="20" <?php if ($pages->items_per_page == 20) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','20 items per page');?></option>
                    <option value="40" <?php if ($pages->items_per_page == 40) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','40 items per page');?></option>
                    <option value="60" <?php if ($pages->items_per_page == 60) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','60 items per page');?></option>
                    <option value="80" <?php if ($pages->items_per_page == 80) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','80 items per page');?></option>
                    <option value="100" <?php if ($pages->items_per_page == 100) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','100 items per page');?></option>
                    <option value="150" <?php if ($pages->items_per_page == 150) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','150 items per page');?></option>
                    <option value="200" <?php if ($pages->items_per_page == 200) : ?>selected="selected"<?php endif; ?> ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','200 items per page');?></option>
                </select>

                <?php if ($pages->items_total > 0) : $appendPrintExportURL = '';?>
                <?php include(erLhcoreClassDesign::designtpl('lhmailconv/lists/search_panel_append_print_multiinclude.tpl.php'));?>

                <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhmailconv','export_mails')) : ?>
                <button type="button" onclick="return lhc.revealModal({'title' : 'Export', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(export)/1?<?php echo $appendPrintExportURL?>'})" class="btn btn-outline-secondary btn-sm"><span class="material-icons">file_download</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Export')?> (<?php echo $pages->items_total?>)</button>
                <?php endif; ?>

                <?php endif; ?>

                <a class="btn btn-outline-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/sendemail')?>">
                    <i class="material-icons">email</i>
                    <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','New e-mail')?>
                </a>

                <a class="btn btn-outline-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/conversations')?>/(sortby)/statuspriority/(conversation_status_ids)/1/0/(user_ids)/<?php echo erLhcoreClassUser::instance()->getUserID()?>"><i class="material-icons">account_box</i> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','My active/new mails');?></a>

                <?php if ($pages->items_total > 0 || isset($_GET['doSearch'])) : ?>

                    <?php if ($input->view > 0) : ?>
                        <input type="hidden" name="view" value="<?php echo $input->view?>" />
                    <?php endif; ?>

                    <?php if (!isset($is_archive_mode) && erLhcoreClassUser::instance()->hasAccessTo('lhviews','use_mail')) : ?>
                        <button type="button" onclick="return lhc.revealModal({'title' : 'Export', 'height':350, backdrop:true, 'url':'<?php echo $pages->serverURL?>/(export)/2'})" class="btn btn-outline-secondary btn-sm">
                            <span class="material-icons">saved_search</span>
                            <?php if ($input->view > 0) : ?>
                                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Update view')?>
                            <?php else : ?>
                                <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Save as view')?>
                            <?php endif; ?>
                        </button>
                    <?php endif; ?>

                <?php endif; ?>
                
                <a class="btn btn-outline-secondary btn-sm" href="<?php if (!isset($is_archive_mode)) : ?><?php echo erLhcoreClassDesign::baseurl('mailconv/conversations')?><?php else : ?>sss<?php endif; ?>"><span class="material-icons">refresh</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Reset');?></a>

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