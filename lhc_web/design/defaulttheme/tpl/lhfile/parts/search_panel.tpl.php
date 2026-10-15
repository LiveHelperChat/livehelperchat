<form action="<?php echo $input->form_action?>" method="get" id="file-search-form" ng-non-bindable>

    <input type="hidden" name="doSearch" value="1">

    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat ID');?></label>
                <input type="text" id="chat_id" class="form-control form-control-sm" placeholder="<?php echo htmlspecialchars("<id>[,<id>]");?>" name="chat_id" value="<?php echo htmlspecialchars((string)$input->chat_id)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/user_title.tpl.php')); ?>
                <label><?php echo $userTitle['user'];?></label>
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
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','File name');?></label>
                <input type="text" id="file_upload_name" class="form-control form-control-sm" name="upload_name" value="<?php echo htmlspecialchars($input->upload_name)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','File id');?></label>
                <input type="text" id="file_id" class="form-control form-control-sm" name="file_id" value="<?php echo htmlspecialchars((string)$input->file_id)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Extension');?></label>
                <input type="text" id="file_extension" class="form-control form-control-sm" placeholder="pdf,docx" name="extension" value="<?php echo htmlspecialchars((string)$input->extension)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label class="col-form-label"><input type="checkbox" id="file_visitor" name="visitor" <?php if ($input->visitor === 0) : ?>checked="checked"<?php endif; ?> value="0"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Only visitor files')?></label>
                <label class="col-form-label d-block"><input type="checkbox" id="file_persistent" name="persistent" <?php if ($input->persistent == 1) : ?>checked="checked"<?php endif; ?> value="1"> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Only persistent')?></label>
            </div>
        </div>
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
                $('#file_upload_name,#file_id,#file_extension,#chat_id').keyup(function() {
                    clearTimeout(timeout);
                    timeout = setTimeout(function(){
                        $('#file-search-form').submit();
                        return false;
                    },300);
                });
                $(document).on('change', '#id_user_id,#file_visitor,#file_persistent,#id_timefrom,#id_timeto,#id_timefrom_hours,#id_timefrom_minutes,#id_timefrom_seconds,#id_timeto_hours,#id_timeto_minutes,#id_timeto_seconds', function() {
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


    <div class="row">

        <div class="col-12">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from');?></label>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="text" class="form-control form-control-sm" name="timefrom" id="id_timefrom" autocomplete="off" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars((string)$input->timefrom)?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?> <small>[<?php echo date('H:i:s')?>]</small></label>
                        <div class="row">
                            <div class="col-md-4">
                                <select name="timefrom_hours" id="id_timefrom_hours" class="form-control form-control-sm">
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                                    <?php for ($i = 0; $i <= 23; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timefrom_hours) && $input->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="timefrom_minutes" id="id_timefrom_minutes" class="form-control form-control-sm">
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                                    <?php for ($i = 0; $i <= 59; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timefrom_minutes) && $input->timefrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="timefrom_seconds" id="id_timefrom_seconds" class="form-control form-control-sm">
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
                                <input type="text" class="form-control form-control-sm" name="timeto" id="id_timeto" autocomplete="off" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars((string)$input->timeto)?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?> <small>[<?php echo date('H:i:s')?>]</small></label>
                        <div class="row">
                            <div class="col-md-4">
                                <select name="timeto_hours" id="id_timeto_hours" class="form-control form-control-sm">
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                                    <?php for ($i = 0; $i <= 23; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timeto_hours) && $input->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="timeto_minutes" id="id_timeto_minutes" class="form-control form-control-sm">
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                                    <?php for ($i = 0; $i <= 59; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timeto_minutes) && $input->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="timeto_seconds" id="id_timeto_seconds" class="form-control form-control-sm">
                                    <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                                    <?php for ($i = 0; $i <= 59; $i++) : ?>
                                        <option value="<?php echo $i?>" <?php if (isset($input->timeto_seconds) && $input->timeto_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                                    <?php endfor;?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="btn-group" role="group" aria-label="...">
                <button type="submit" class="btn btn-primary btn-sm no-wrap" name="doSearch"><span class="material-icons">search</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?></button>

                <?php if ($pages->items_total > 0 || isset($_GET['doSearch'])) : ?>
                    <a class="btn btn-outline-secondary btn-sm" href="<?php echo $input->form_action?>"><span class="material-icons">refresh</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Reset');?></a>
                <?php endif; ?>
            </div>
        </div>

        

    </div>
</form>

<script>
    (function() {
        $('#id_timefrom,#id_timeto').fdatepicker({
            format: 'yyyy-mm-dd'
        });
    })();
</script>