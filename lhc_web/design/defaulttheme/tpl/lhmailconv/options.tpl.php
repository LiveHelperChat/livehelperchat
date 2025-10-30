<h1 class="attr-header"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Mail conversations options')?></h1>

<form action="" method="post" ng-non-bindable>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <?php if (isset($updated) && $updated == 'done') : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/onlineusers','Settings updated'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
    <?php endif; ?>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','MCE Plugins')?> <button id="mce_plugins_default" type="button" class="btn btn-xs btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Set default')?></button></label>
        <textarea rows="5" class="form-control" id="mce_plugins_value" name="mce_plugins"><?php isset($mc_options['mce_plugins']) ? print htmlspecialchars($mc_options['mce_plugins']) : ''?></textarea>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','MCE Toolbar')?> <button id="mce_toolbar_default" type="button" class="btn btn-xs btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Set default')?></button></label>
        <textarea rows="5" class="form-control" id="mce_toolbar_value" name="mce_toolbar"><?php isset($mc_options['mce_toolbar']) ? print htmlspecialchars($mc_options['mce_toolbar']) : ''?></textarea>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="disable_auto_owner" value="on" <?php if (isset($mc_options['disable_auto_owner']) && ($mc_options['disable_auto_owner'] == true)) : ?>checked="checked"<?php endif;?> />
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Disable becoming owner automatically on conversation open event')?></label>
        <p class="mb-0"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Operator will become an owner if they click the reply button.')?></small></p>
    </div>
    
    <div class="form-group">
        <label>
            <input type="checkbox" name="skip_images" value="on" <?php if (isset($mc_options['skip_images']) && ($mc_options['skip_images'] == true)) : ?>checked="checked"<?php endif;?> />
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Skip directly included images while replying to e-mail')?></label>
        <p class="mb-0"><small><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Image will be replaced with Image skipped text')?></small></p>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="no_quote_mail" value="on" <?php if (isset($mc_options['no_quote_mail']) && ($mc_options['no_quote_mail'] == true)) : ?>checked="checked"<?php endif;?> />
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Do not quote original e-mail while responding to a ticket')?></label>
    </div>
    <div class="form-group ps-4">
        <label>
            <input type="checkbox" name="keep_forward_quote" value="on" <?php if (isset($mc_options['keep_forward_quote']) && ($mc_options['keep_forward_quote'] == true)) : ?>checked="checked"<?php endif;?> />
            <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Preserve the quote if the message is an e-mail forward')?></label>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Skipped image replacement text. You can use emoji also e.g')?> &#128444;&#65039;&nbsp;<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Copy')?>&nbsp;<span class="badge bg-secondary"><?php echo htmlspecialchars('&#128444;&#65039;')?></span></label>
        <input type="text" class="form-control form-control-sm" name="image_skipped_text" value="<?php isset($mc_options['image_skipped_text']) ? print htmlspecialchars($mc_options['image_skipped_text']) : ''?>" placeholder="[img]"/>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Reply to template')?> <button id="reply_toolbar_default" type="button" class="btn btn-xs btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Set default')?></button></label>
        <textarea rows="5" class="form-control" id="id_reply_to_tmp" name="reply_to_tmp"><?php isset($mc_options['reply_to_tmp']) ? print htmlspecialchars($mc_options['reply_to_tmp']) : ''?></textarea>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Forward to template:')?> <button id="forward_toolbar_default" type="button" class="btn btn-xs btn-secondary"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Set default')?></button></label>
        <textarea rows="5" class="form-control" id="id_forward_to_tmp" name="forward_to_tmp"><?php isset($mc_options['forward_to_tmp']) ? print htmlspecialchars($mc_options['forward_to_tmp']) : ''?></textarea>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Download view mode')?></label>
        <select class="form-control" name="download_view_mode">
            <option value="0" <?php if (!isset($mc_options['download_view_mode']) || $mc_options['download_view_mode'] == 0) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Default (download file)')?></option>
            <option value="1" <?php if (isset($mc_options['download_view_mode']) && $mc_options['download_view_mode'] == 1) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','View in new tab')?></option>
            <option value="2" <?php if (isset($mc_options['download_view_mode']) && $mc_options['download_view_mode'] == 2) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Modal')?></option>
        </select>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','File download restrictions')?></label>
        <select class="form-control" name="file_download_mode" id="file_download_mode">
            <option value="0" <?php if (!isset($mc_options['file_download_mode']) || $mc_options['file_download_mode'] == 0) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Allow to download all files')?></option>
            <option value="1" <?php if (isset($mc_options['file_download_mode']) && $mc_options['file_download_mode'] == 1) : ?>selected="selected"<?php endif;?>><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Restrict file downloads by extension')?></option>
        </select>
    </div>

    <div class="form-group" id="extension_settings" style="<?php echo (!isset($mc_options['file_download_mode']) || $mc_options['file_download_mode'] == 0) ? 'display:none;' : ''; ?>">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','File extension settings')?></h5>
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Allowed extensions for all operators')?></label>
                    <input type="text" class="form-control" name="allowed_extensions_public" value="<?php isset($mc_options['allowed_extensions_public']) ? print htmlspecialchars($mc_options['allowed_extensions_public']) : ''?>" placeholder="jpg|jpeg|png|gif|pdf|doc|docx"/>
                    <small class="form-text text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Pipe separated list of file extensions that can be downloaded by all operators without special permissions')?></small>
                </div>
                <div class="form-group">
                    <label class="pb-1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Restricted extensions (require special permission)')?> <span class="badge bg-info">lhmailconv,download_restricted</span></label>
                    <input type="text" class="form-control" name="allowed_extensions_restricted" value="<?php isset($mc_options['allowed_extensions_restricted']) ? print htmlspecialchars($mc_options['allowed_extensions_restricted']) : ''?>" placeholder="zip|rar|exe|bat|sh"/>
                    <small class="form-text text-muted"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Pipe separated list of file extensions that can only be downloaded by operators with special permissions')?></small>
                </div>
                 <div class="form-group">
                    <label><input type="checkbox" name="check_suspicious_pdf" value="on" <?php if (isset($mc_options['check_suspicious_pdf']) && ($mc_options['check_suspicious_pdf'] == true)) : ?>checked="checked"<?php endif;?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvfile','Check for suspicious PDF files content')?></label>
                </div>
            </div>
        </div>
    </div>
    

    <input type="submit" class="btn btn-secondary" name="StoreOptions" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save'); ?>" />

    <script>
        $(document).ready(function() {
            $('#mce_plugins_default').click(function(){
                $('#mce_plugins_value').val("[\"advlist autolink lists link image charmap print preview anchor image lhfiles\",\n\"searchreplace visualblocks code fullscreen\",\n\"media table paste help\",\n\"print preview importcss searchreplace autolink save directionality visualblocks visualchars fullscreen media codesample charmap pagebreak nonbreaking anchor advlist lists wordcount textpattern noneditable help charmap emoticons\"]");
            });
            $('#mce_toolbar_default').click(function(){
                $('#mce_toolbar_value').val("undo redo | fontselect formatselect fontsizeselect | table | paste pastetext | subscript superscript | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | lhtemplates lhfiles insertfile image pageembed template link anchor codesample | bullist numlist outdent indent | removeformat permanentpen | charmap emoticons | fullscreen print preview paste code | help");
            });
            $('#reply_toolbar_default').click(function(){
                $('#id_reply_to_tmp').val("<p>On {args.msg.udate__datef__Y-m-d H:i} {args.msg.from_name__or__msg.from_address} wrote:</p>");
            });
            $('#forward_toolbar_default').click(function(){
                $('#id_forward_to_tmp').val(<?php echo json_encode(implode("\n",[
                        "---------- Forwarded message ---------<br/>",
                        "From: {args.msg.from_name__not_empty__<b>}{args.msg.from_name}{args.msg.from_name__not_empty__</b> }<{args.msg.from_address}><br/>",
                        "Date: {args.msg.udate__datef__D}, {args.msg.udate__datef__d}, {args.msg.udate__datef__M} {args.msg.udate__datef__Y} at {args.msg.udate__datef__H:i}<br/>",
                        "Subject: {args.msg.subject}<br/>",
                        "To: {args.msg.to_data_front}<br/>",
                        "{args.msg.cc_data_front__not_empty__Cc: }{args.msg.cc_data_front}{args.msg.cc_data_front__not_empty__<br/>}",
                        "{args.msg.bcc_data_front__not_empty__Bcc: }{args.msg.bcc_data_front}{args.msg.bcc_data_front__not_empty__<br/>}"
                        ])); ?>);
            });
            
            // Toggle extension settings visibility
            $('#file_download_mode').change(function(){
                if ($(this).val() == '1') {
                    $('#extension_settings').show();
                } else {
                    $('#extension_settings').hide();
                }
            });
        });
    </script>

</form>
