<form action="<?php echo $input->form_action?>" method="get" id="file-search-form">

    <input type="hidden" name="doSearch" value="1">

    <div class="row">
        <div class="col-6">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <?php echo erLhcoreClassRenderHelper::renderCombobox( array (
                            'input_name'     => 'user_id',
                            'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select user'),
                            'selected_id'    => $input->user_id,
                            'css_class' => 'form-control',
                            'list_function_params' => erLhcoreClassGroupUser::getConditionalUserFilter(),
                            'list_function'  => 'erLhcoreClassModelUser::getUserList'
                        )); ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <input type="text" id="file_upload_name" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','File name')?>" class="form-control" name="upload_name" value="<?php echo htmlspecialchars($input->upload_name)?>" />
                    </div>
                </div>
            </div>
            <label><input type="checkbox" id="file_visitor" name="visitor" <?php if ($input->visitor === 0) : ?>checked="checked"<?php endif; ?> value="0">Only visitor files</label>
            <label><input type="checkbox" id="file_persistent" name="persistent" <?php if ($input->persistent == 1) : ?>checked="checked"<?php endif; ?> value="1">Only persistent</label>
        </div>

        <?php if (isset($fileSearchOptions['ajax']) && $fileSearchOptions['ajax'] === true) : ?>
            <input type="hidden" name="ajax_search" value="1">
        <script>
            (function() {
                $('#file-search-form').on('submit',function () {
                    var form = $(this);
                    $.get(form.attr('action'),form.serialize(), function(data) {
                        $('#file-search-content').html(data);
                    });

                    return false;
                });
                var timeout = null;
                $('#file_upload_name').keyup(function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(function(){
                        $('#file-search-form').submit();
                        return false;
                    },300);
                });
                $('#id_user_id,#file_visitor,#file_persistent').change(function() {
                    $('#file-search-form').submit();
                });
            })();
        </script>
        <?php else : ?>
        <div class="col-6">
            <input type="submit" name="doSearch" class="btn btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
        </div>
        <?php endif; ?>
    </div>
</form>