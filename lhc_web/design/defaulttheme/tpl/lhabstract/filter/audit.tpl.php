<form action="<?php echo erLhcoreClassDesign::baseurl('abstract/list')?>/Audit" method="get" ng-non-bindable>
<div class="row">

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from');?></label>
            <div class="row">
                <div class="col-md-12">
                    <input type="text" class="form-control form-control-sm" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input_form->timefrom != '' ? $input_form->timefrom : date('Y-m-d', time()-24*3600))?>" />
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?> <small>[<?php echo date('H:i:s')?>]</small></label>
            <div class="row">
                <div class="col-md-4">
                    <select name="timefrom_hours" class="form-control form-control-sm">
                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                        <?php for ($i = 0; $i <= 23; $i++) : ?>
                            <option value="<?php echo $i?>" <?php if (isset($input_form->timefrom_hours) && $input_form->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                        <?php endfor;?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="timefrom_minutes" class="form-control form-control-sm">
                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                        <?php for ($i = 0; $i <= 59; $i++) : ?>
                            <option value="<?php echo $i?>" <?php if (isset($input_form->timefrom_minutes) && $input_form->timefrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                        <?php endfor;?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="timefrom_seconds" class="form-control form-control-sm">
                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                        <?php for ($i = 0; $i <= 59; $i++) : ?>
                            <option value="<?php echo $i?>" <?php if (isset($input_form->timefrom_seconds) && $input_form->timefrom_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
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
                    <input type="text" class="form-control form-control-sm" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input_form->timeto)?>" />
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?> <small>[<?php echo date('H:i:s')?>]</small></label>
            <div class="row">
                <div class="col-md-4">
                    <select name="timeto_hours" class="form-control form-control-sm">
                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                        <?php for ($i = 0; $i <= 23; $i++) : ?>
                            <option value="<?php echo $i?>" <?php if (isset($input_form->timeto_hours) && $input_form->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                        <?php endfor;?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="timeto_minutes" class="form-control form-control-sm">
                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                        <?php for ($i = 0; $i <= 59; $i++) : ?>
                            <option value="<?php echo $i?>" <?php if (isset($input_form->timeto_minutes) && $input_form->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                        <?php endfor;?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="timeto_seconds" class="form-control form-control-sm">
                        <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                        <?php for ($i = 0; $i <= 59; $i++) : ?>
                            <option value="<?php echo $i?>" <?php if (isset($input_form->timeto_seconds) && $input_form->timeto_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                        <?php endfor;?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Object ID');?></label>
            <input type="text" class="form-control form-control-sm" name="object_id" value="<?php echo htmlspecialchars((string)$input_form->object_id)?>" />
        </div>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Category');?></label>
            <input type="text" list="category_list" class="form-control form-control-sm" name="category" value="<?php echo htmlspecialchars($input_form->category)?>" />
        </div>
        <datalist id="category_list" autocomplete="new-password">
            <option value="js">js</option>
            <option value="block">block</option>
            <option value="store">store</option>
            <option value="cronjob_exception">cronjob_exception</option>
            <option value="cronjob_fatal">cronjob_fatal</option>
            <option value="slow_view">slow_view</option>
            <option value="slow_request">slow_request</option>
            <option value="web_exception">Internal web error has accoured</option>
            <option value="bot">bot - bot related logged actions</option>
            <option value="update_active_chats">update_active_chats</option>
            <option value="files">files - failed visitors files upload</option>
            <option value="translation">translation - translations failures</option>
            <option value="translation_item">translation_item - translating translation item failed</option>
            <option value="Departament">Department</option>
            <option value="Subject">Subject</option>
            <option value="User">User</option>
            <option value="AutoResponder">AutoResponder</option>
            <option value="AutoResponderDelete">AutoResponderDelete</option>
            <option value="CannedMsg">CannedMsg</option>
            <option value="CannedMsgDelete">CannedMsgDelete</option>
            <option value="ChatConfig">ChatConfig</option>
            <option value="incoming_webhook_parse">Incoming webhook parse failures</option>
            <option value="incoming_webhook">Incoming webhook request</option>
            <option value="extract_department">Invalid department argument</option>
            <?php include(erLhcoreClassDesign::designtpl('lhabstract/filter/audit/category_list_multiinclude.tpl.php'));?>
        </datalist>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Source');?></label>
            <input type="text" list="source_list" class="form-control form-control-sm" name="source" value="<?php echo htmlspecialchars($input_form->source)?>" />
        </div>
        <datalist id="source_list" autocomplete="new-password">
            <option value="lhc">lhc</option>
            <option value="View">View</option>
            <?php include(erLhcoreClassDesign::designtpl('lhabstract/filter/audit/source_list_multiinclude.tpl.php'));?>
        </datalist>
    </div>

    <div class="col-md-2">
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Message body');?></label>
            <input type="text" class="form-control form-control-sm" name="message" value="<?php echo htmlspecialchars((string)$input_form->message)?>" />
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <input type="submit" class="btn btn-sm btn-secondary" value="Search" name="doSearch">
        </div>
    </div>
</div>
</form>
<script>
    $(function() {
        $('#id_timefrom,#id_timeto').fdatepicker({
            format: 'yyyy-mm-dd'
        });
    });
</script>