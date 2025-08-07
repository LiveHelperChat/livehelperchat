<form action="<?php echo $input->form_action?>" method="get" id="file-search-form" ng-non-bindable>

    <input type="hidden" name="doSearch" value="1">

    <div class="row">
        <div class="col-6">
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <input type="text" id="chat_id" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat id')?>" class="form-control form-control-sm" name="chat_id" value="<?php echo htmlspecialchars((string)$input->chat_id)?>" />
                    </div>
                </div>
                <div class="col-3">
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/user_title.tpl.php')); ?>
                    <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                        'input_name'     => 'user_ids[]',
                        'optional_field' => $userTitle['user_select'],
                        'selected_id'    => $input->user_ids,
                        'css_class'      => 'form-control',
                        'display_name'   => 'name_official',
                        'ajax'           => 'users',
                        'list_function_params' => array_merge(erLhcoreClassGroupUser::getConditionalUserFilter(),array('sort' => '`name` ASC', 'limit' => 50)),
                        'list_function'  => 'erLhcoreClassModelUser::getUserList',
                    )); ?>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <input type="text" id="file_upload_name" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','File name')?>" class="form-control form-control-sm" name="upload_name" value="<?php echo htmlspecialchars($input->upload_name)?>" />
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <input type="text" id="file_id" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','File id')?>" class="form-control form-control-sm" name="file_id" value="<?php echo htmlspecialchars((string)$input->file_id)?>" />
                    </div>
                </div>
            </div>
            <label><input type="checkbox" id="file_visitor" name="visitor" <?php if ($input->visitor === 0) : ?>checked="checked"<?php endif; ?> value="0"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Only visitor files')?></label>
            <label><input type="checkbox" id="file_persistent" name="persistent" <?php if ($input->persistent == 1) : ?>checked="checked"<?php endif; ?> value="1"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Only persistent')?></label>
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
                $('#file_upload_name,#file_id,#chat_id').keyup(function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(function(){
                        $('#file-search-form').submit();
                        return false;
                    },300);
                });
                $(document).on('change', '#id_user_id,#file_visitor,#file_persistent', function() {
                    $('#file-search-form').submit();
                });
                $('.btn-block-department').makeDropdown({
                    "on_select" : function() {
                        $('#file-search-form').submit();
                    } ,
                    "on_delete" : function() {
                        $('#file-search-form').submit();
                    }
                });
            })();
        </script>
        <?php else : ?>
            <script>
                $(function() {
                    $('.btn-block-department').makeDropdown();
                });
            </script>
        <?php endif; ?>

        <div class="col-6">
            <input type="submit" name="doSearch" class="btn btn-sm btn-secondary" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
        </div>

        

    </div>
</form>