<h1>Send e-mail</h1>

<?php if (isset($updated)) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','E-mail was send.'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<form action="" method="post">

    <div class="form-group">
        <label>Mailbox</label>
        <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
            'input_name'     => 'mailbox_id',
            'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select a mailbox'),
            'selected_id'    => $item->mailbox_id,
            'css_class'      => 'form-control form-control-sm',
            'list_function'  => 'erLhcoreClassModelMailconvMailbox::getList'
        )); ?>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Subject');?></label>
        <input type="text" class="form-control form-control-sm" name="subject" value="<?php echo htmlspecialchars($item->subject)?>" />
    </div>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Recipient E-mail');?></label>
                <input type="text" class="form-control form-control-sm" name="from_address" value="<?php echo htmlspecialchars($item->from_address)?>" />
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Recipient Name');?></label>
                <input type="text" class="form-control form-control-sm" name="from_name" value="<?php echo htmlspecialchars($item->from_name)?>" />
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Reply to e-mail');?></label>
                <input type="text" class="form-control form-control-sm" name="to_data" value="<?php echo htmlspecialchars($item->to_data)?>" />
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Reply to name');?></label>
                <input type="text" class="form-control form-control-sm" name="reply_to_data" value="<?php echo htmlspecialchars($item->reply_to_data)?>" />
            </div>
        </div>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Body');?></label>
        <textarea id="response-template" class="form-control form-control-sm" name="body"><?php echo htmlspecialchars($item->body)?></textarea>
    </div>

    <script>
        $(document).ready(function(){
            tinymce.init({
                selector: '#response-template',
                height: 320,
                automatic_uploads: true,
                file_picker_types: 'image',
                images_upload_url: '<?php echo erLhcoreClassDesign::baseurl('mailconv/uploadimage')?>',
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

    <div>
        <button name="SendEmail" class="btn btn-sm btn-secondary" type="submit">Send an e-mail</button>
    </div>

</form>