<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Edit');?></h1>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Updated'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form autocomplete="off" action="<?php echo erLhcoreClassDesign::baseurl('mailconv/editmailbox')?>/<?php echo $item->id?>" method="post" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li role="presentation" class="nav-item"><a href="#settings" class="nav-link<?php if ($tab == '') : ?> active<?php endif;?>" aria-controls="settings" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Login settings');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'tab_options') : ?> active<?php endif;?>" href="#options" aria-controls="options" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Options');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'tab_mailbox') : ?> active<?php endif;?>" href="#mailbox" aria-controls="mailbox" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Mailbox');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'tab_signature') : ?> active<?php endif;?>" href="#signature" aria-controls="signature" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Signature');?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link<?php if ($tab == 'tab_utilities') : ?> active<?php endif;?>" href="#utilities" aria-controls="utilities" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Utilities');?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="settings">
            <?php include(erLhcoreClassDesign::designtpl('lhmailconv/parts/form.tpl.php'));?>

            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" class="btn btn-secondary" name="Save_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
                <input type="submit" class="btn btn-secondary" name="Update_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
                <input type="submit" class="btn btn-secondary" name="Cancel_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_mailbox') : ?>active<?php endif;?>" id="mailbox">
            <a class="btn btn-secondary btn-sm" title="Mailboxes" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/editmailbox')?>/<?php echo $item->id?>/(action)/mailbox?r=<?php echo time()?>#!#mailbox" ><i class="material-icons">sync</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Get mailbox to sync');?></a>

            <hr>

            <div class="row">
                <div class="col-4">
                    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/mailconvmb','Choose what mailbox you want to sync');?></h5>
                </div>
                <div class="col-4">
                    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/mailconvmb','Choose where deleted e-mails should be moved');?></h5>
                </div>
                <div class="col-4">
                    <h5><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('mailconv/mailconvmb','Choose a send folder');?></h5>
                </div>
            </div>
            <?php foreach ($item->mailbox_sync_array as $mailbox) : ?>
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label><input type="checkbox" value="<?php echo htmlspecialchars($mailbox['path'])?>" <?php if ($mailbox['sync'] == true) : ?>checked="checked"<?php endif; ?> name="Mailbox[]"> <?php echo htmlspecialchars($mailbox['path'])?></label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label><input type="radio" value="<?php echo htmlspecialchars($mailbox['path'])?>" <?php if (isset($mailbox['sync_deleted']) && $mailbox['sync_deleted'] == true) : ?>checked="checked"<?php endif; ?> name="MailboxDeleted"> <?php echo htmlspecialchars($mailbox['path'])?></label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label><input type="radio" value="<?php echo htmlspecialchars($mailbox['path'])?>" <?php if (isset($mailbox['send_folder']) && $mailbox['send_folder'] == true) : ?>checked="checked"<?php endif; ?> name="MailboxSend"> <?php echo htmlspecialchars($mailbox['path'])?></label>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" class="btn btn-secondary" name="Save_mailbox" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
            </div>

        </div>

        <div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_options') : ?>active<?php endif;?>" id="options">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label><input type="checkbox" name="active" value="on" <?php $item->active == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Active');?></label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label><input type="checkbox" name="delete_mode" value="on" <?php $item->delete_mode == erLhcoreClassModelMailconvMailbox::DELETE_ALL ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','On message deletion delete it also on IMAP server');?></label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label><input type="checkbox" name="create_a_copy" value="on" <?php $item->create_a_copy == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Create a copy in a send folder.');?></label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label><input type="checkbox" name="assign_parent_user" value="on" <?php $item->assign_parent_user == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Assign follow-up e-mail to the previous thread owner');?></label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Check for new messages interval in seconds.');?></label>
                        <input type="text" placeholder="60" maxlength="250" class="form-control form-control-sm" name="sync_interval" value="<?php echo htmlspecialchars($item->sync_interval)?>" />
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Import since this unix timestamp.');?> <button type="button" class="btn btn-xs btn-secondary" onclick="$('#id_import_since').val(Math.floor(Date.now()/1000))"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Set to now');?></button></label>
                        <input type="number" maxlength="250" class="form-control form-control-sm" id="id_import_since" name="import_since" value="<?php echo htmlspecialchars($item->import_since)?>" />
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Timeout in days after last response before we create a new issue');?></label>
                        <input type="number" maxlength="250" class="form-control form-control-sm" name="reopen_timeout" value="<?php echo htmlspecialchars($item->reopen_timeout)?>" />
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Import priority. If mail is send to two mailbox and we handle both, only mail with higher mailbox priority will be processed.');?></label>
                        <input type="number" maxlength="250" class="form-control form-control-sm" name="import_priority" value="<?php echo htmlspecialchars($item->import_priority)?>" />
                    </div>
                </div>
            </div>

            <div class="row pb-2">
                <div class="col-6">
                    <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                        'input_name'     => 'user_id',
                        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb', 'Choose a user'),
                        'selected_id'    => [$item->user_id],
                        'ajax'           => 'users',
                        'data_prop'      => 'data-limit="1" data-type="radio" data-noselector="1"',
                        'css_class'      => 'form-control',
                        'type'           => 'radio',
                        'display_name'   => 'name_official',
                        'no_selector'    => true,
                        'list_function_params' => array('limit' => 20),
                        'list_function'  => 'erLhcoreClassModelUser::getList',
                    )); ?>
                </div>
                <div class="col-6">
                    <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                        'input_name'     => 'dep_id',
                        'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb', 'Default department'),
                        'selected_id'    => [$item->dep_id],
                        'ajax'           => 'deps',
                        'data_prop'      => 'data-limit="1" data-type="radio" data-noselector="1"',
                        'css_class'      => 'form-control',
                        'display_name'   => 'name',
                        'type'           => 'radio',
                        'no_selector'    => true,
                        'list_function_params' => array('limit' => 20, 'sort' => '`name` ASC'),
                        'list_function'  => 'erLhcoreClassModelDepartament::getList',
                    )); ?>
                    <p><small><i>Default department is only used for replaceable variables support at the moment.</i></small></p>
                </div>
                <script>
                    $(function() {
                        $('.btn-block-department').makeDropdown();
                    });
                </script>
            </div>

            <div class="row pb-2">
                <div class="col-6">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Close conversations older than n days. Only conversations of the active mailboxes will be closed.');?></label>
                        <input type="number" class="form-control form-control-sm" name="workflow_auto_close" value="<?php isset($item->workflow_options_array['auto_close']) ? print htmlspecialchars($item->workflow_options_array['auto_close']) : ''?>" />
                    </div>
                </div>
                <div class="col-6">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','What status conversations we should close?');?></label>
                    <div><label><input type="checkbox" name="workflow_close_status[]" <?php isset($item->workflow_options_array['close_status']) && (in_array(erLhcoreClassModelMailconvConversation::STATUS_PENDING,$item->workflow_options_array['close_status'])) ? print 'checked="checked"' : ''?> value="<?php echo erLhcoreClassModelMailconvConversation::STATUS_PENDING?>" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Pending');?></label></div>
                    <div><label><input type="checkbox" name="workflow_close_status[]" <?php isset($item->workflow_options_array['close_status']) && (in_array(erLhcoreClassModelMailconvConversation::STATUS_ACTIVE,$item->workflow_options_array['close_status'])) ? print 'checked="checked"' : ''?> value="<?php echo erLhcoreClassModelMailconvConversation::STATUS_ACTIVE?>" /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Active');?></label></div>
                </div>
            </div>

            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" class="btn btn-secondary" name="SaveOptions_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
                <input type="submit" class="btn btn-secondary" name="UpdateOptions_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
                <input type="submit" class="btn btn-secondary" name="Cancel_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
            </div>

        </div>

        <div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_utilities') : ?>active<?php endif;?>" id="utilities">
            <a class="btn btn-secondary btn-sm" title="Sync messages" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/editmailbox')?>/<?php echo $item->id?>/(action)/sync?r=<?php echo time()?>#!#utilities" ><i class="material-icons">sync</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Check for a new messages');?></a>

            <ul>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Last sync finished');?> - <?php echo $item->last_sync_time_ago?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','ago');?>.</li>
                <li><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Last sync started');?> - <?php echo $item->sync_started_ago?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','ago');?>.</li>
            </ul>

            <p>
                <h6><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','UUID Next status')?></h6>
                <code><?php echo htmlspecialchars($item->uuid_status);?></code>
            </p>

            <h5 class="mt-4"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Sync log');?></h5>
            <pre><?php echo htmlspecialchars(print_r($item->last_sync_log_array,true))?></pre>

        </div>

        <div role="tabpanel" class="tab-pane <?php if ($tab == 'tab_signature') : ?>active<?php endif;?>" id="signature">

            <div class="form-group">
                <label><input type="checkbox" name="signature_under" value="on" <?php $item->signature_under == 1 ? print ' checked="checked" ' : ''?> > <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Put signature directly under reply');?></label>
            </div>
            
            <div class="form-group">
                <textarea name="signature" id="signature-editor" rows="10" class="form-control form-control-sm"><?php echo htmlspecialchars($item->signature)?></textarea>
                <script>
                    $(document).ready(function(){
                        tinymce.init({
                            selector: '#signature-editor',
                            height: 320,
                            automatic_uploads: true,
                            file_picker_types: 'image',
                            images_upload_url: '<?php echo erLhcoreClassDesign::baseurl('mailconv/uploadimage')?>/(csrf)/'+confLH.csrf_token,
                            paste_data_images: true,
                            relative_urls : false,
                            browser_spellcheck: true,
                            paste_as_text: true,
                            contextmenu: false,
                            menubar: false,
                            plugins: [
                                'advlist autolink lists link image charmap print preview anchor image lhfiles',
                                'searchreplace visualblocks code fullscreen',
                                'media table paste help',
                                'print preview importcss searchreplace autolink save autosave directionality visualblocks visualchars fullscreen media template codesample charmap pagebreak nonbreaking anchor toc advlist lists wordcount textpattern noneditable help charmap emoticons'
                            ],
                            toolbar_mode: 'wrap',
                            toolbar:
                                'undo redo | fontselect formatselect fontsizeselect | table | paste pastetext | subscript superscript | bold italic underline strikethrough | forecolor backcolor | \
                                alignleft aligncenter alignright alignjustify | lhfiles insertfile image pageembed template link anchor codesample | \
                                bullist numlist outdent indent | removeformat permanentpen | charmap emoticons | fullscreen print preview paste code | help'
                        });
                    });
                </script>
                <p><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Supported replaceable variable.');?>
                        {operator} - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Real operator Name and Surname')?>,
                        {department},
                        {operator_chat_name} - <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Operator chat Nick name if filled, otherwise real  Name and Surname')?>
                    </small></p>
            </div>

            <div class="btn-group" role="group" aria-label="...">
                <input type="submit" class="btn btn-secondary" name="Save_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
                <input type="submit" class="btn btn-secondary" name="UpdateSignature_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
                <input type="submit" class="btn btn-secondary" name="Cancel_page" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
            </div>

        </div>

    </div>

</form>