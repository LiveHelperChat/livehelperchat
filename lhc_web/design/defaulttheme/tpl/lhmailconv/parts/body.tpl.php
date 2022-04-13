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
            cache_suffix: "?v=1.0",
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
            plugins: <?php echo json_encode($mcePlugins)?>,
            toolbar_mode: 'wrap',
            toolbar: <?php echo json_encode($mceToolbar)?>
        });
    });
</script>