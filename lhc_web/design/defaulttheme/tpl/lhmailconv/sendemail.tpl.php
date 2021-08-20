<h1><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Send an e-mail');?></h1>

<?php if (isset($updated)) :
    $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','E-mail was send.');

    if (isset($outcome['copy']) && $outcome['copy']['success'] == true) {
        $msg .= ' ' . erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvmb','Send e-mail copy was created in a send folder.');
    }
?>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>

    <?php if (isset($outcome['copy'])) : ?>
        <?php if ($outcome['copy']['success'] == false) : ?>
            <?php $errors = [$outcome['copy']['reason']]; ?>
            <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
        <?php endif; ?>
    <?php endif; ?>

<?php endif; ?>

<?php if (isset($errors)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated)) : ?>
<a class="btn btn-sm btn-outline-secondary" href="<?php echo erLhcoreClassDesign::baseurl('mailconv/sendemail')?>?new=1"><span class="material-icons">mail</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Send a new e-mail');?></a>
<?php endif; ?>

<?php if (isset($updated) && isset($outcome['copy']['success']) && $outcome['copy']['success'] == true && isset($outcome['copy']['message_id'])) : ?>
    <div class="py-2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Ticket');?>: <span id="ticket-progress"><span class="material-icons lhc-spin">cached</span> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Working');?>...</span></div>
    <script>
        (function intervalStarter() {
                var counter = 0;
                var interval = setInterval(function () {
                $.postJSON(WWW_DIR_JAVASCRIPT + 'mailconv/geticketbymessageid/',{'counter': counter, 'mailbox_id': <?php echo $item->mailbox_id?>, 'message_id': <?php echo json_encode($outcome['copy']['message_id'])?>}, function (data) {
                    if (data.found == true) {
                        $('#ticket-progress').html(data.conversation);
                        clearInterval(interval);
                    }
                });
                counter = counter + 1;
            }, 2000);
        })();
    </script>

<?php endif; ?>

<?php if (!isset($updated)) : ?>

<form action="<?php echo erLhcoreClassDesign::baseurl('mailconv/sendemail')?>" method="post">

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Mailbox');?></label>
        <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
            'input_name'     => 'mailbox_id',
            'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select a mailbox'),
            'selected_id'    => $item->mailbox_id,
            'css_class'      => 'form-control form-control-sm',
            'list_function'  => 'erLhcoreClassModelMailconvMailbox::getList',
            'list_function_params'  => array('filter' => array('active' => 1))
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
                <input type="text" placeholder="If not filled we will use mailbox e-mail" class="form-control form-control-sm" name="to_data" value="<?php echo htmlspecialchars($item->to_data)?>" />
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Reply to name');?></label>
                <input type="text" placeholder="If not filled we will use mailbox name" class="form-control form-control-sm" name="reply_to_data" value="<?php echo htmlspecialchars($item->reply_to_data)?>" />
            </div>
        </div>
    </div>

    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Body');?></label>
        <textarea id="response-template" class="form-control form-control-sm" name="body"><?php echo htmlspecialchars($item->body)?></textarea>
    </div>

    <script>
        <?php
        $mcOptions = erLhcoreClassModelChatConfig::fetch('mailconv_options');
        $mcOptionsData = (array)$mcOptions->data;

        $mceToolbar = 'undo redo | fontselect formatselect fontsizeselect | table | paste pastetext | subscript superscript |'.
            ' bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify '.
            '| lhtemplates lhfiles insertfile image pageembed link anchor codesample | bullist numlist outdent indent | removeformat permanentpen | charmap emoticons | fullscreen print preview paste code | help';

        if (isset($mcOptionsData['mce_toolbar']) && $mcOptionsData['mce_toolbar'] != '') {
            $mceToolbar = $mcOptionsData['mce_toolbar'];
        }

        $mcePlugins = [
            'advlist autolink lists link image charmap print preview anchor image lhfiles',
            'searchreplace visualblocks code fullscreen',
            'media table paste help',
            'print preview importcss searchreplace autolink save autosave directionality visualblocks visualchars fullscreen media codesample charmap pagebreak nonbreaking anchor toc advlist lists wordcount textpattern noneditable help charmap emoticons'
        ];

        if (isset($mcOptionsData['mce_plugins']) && $mcOptionsData['mce_plugins'] != '') {
            $mcePlugins = json_decode($mcOptionsData['mce_plugins'], true);
        }

        ?>
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
                plugins: <?php echo json_encode($mcePlugins)?>,
                toolbar_mode: 'wrap',
                toolbar: <?php echo json_encode($mceToolbar)?>
            });
        });
    </script>

    <div>
        <button name="SendEmail" onclick="$(this).attr('disabled')" class="btn btn-sm btn-secondary" type="submit"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('module/mailconvrt','Send an e-mail');?></button>
    </div>

</form>
<?php endif; ?>