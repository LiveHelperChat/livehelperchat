<?php

if (isset($_GET['doSearchActions'])) {
    $filterParamsCanned = erLhcoreClassSearchHandler::getParams(array(
        'module' => 'chat',
        'module_file' => 'opactions_search',
        'format_filter' => true,
        'use_override' => true,
        'uparams' => $paramsRequest['user_parameters_unordered']));
} else {
    $filterParamsCanned = erLhcoreClassSearchHandler::getParams(array('module' => 'chat', 'module_file' => 'opactions_search', 'format_filter' => true, 'uparams' => $paramsRequest['user_parameters_unordered']));
}

$inputCanned = $filterParamsCanned['input_form'];

?>

<form action="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id;?>/(tab)/opactions" method="get" ng-non-bindable>
    <input type="hidden" name="doSearchActions" value="1">
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Action type');?></label>
                <?php echo erLhcoreClassRenderHelper::renderMultiDropdown( array (
                    'input_name'     => 'category[]',
                    'optional_field' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Choose action type'),
                    'selected_id'    => $inputCanned->category,
                    'css_class'      => 'form-control',
                    'display_name'   => 'name',
                    'list_function_params' => array(),
                    'list_function'  => function () {
                        $items = array();
                        foreach ([
                                'chat_open' => htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat open')),
                                'chat_view' =>  htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat preview')),
                                'chat_search' =>  htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat search')),
                                'chat_export' => htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat export')),
                                'chat_export_elastic' => htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Chat export elastic')),
                                'mail_open' => htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mail open')),
                                'mail_view' => htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mail preview')),
                                'mail_search' => htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mail search')),
                                'mail_export' => htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mail export')),
                                'mail_export_elastic' => htmlspecialchars_decode(erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Mail export ElasticSearch')),
                                 ] as $itemCategory => $itemName) {
                            $item = new StdClass();
                            $item->name = $itemName;
                            $item->id = $itemCategory;
                            $items[] = $item;
                        }
                        return $items;
                    }
                )); ?>
            </div>
        </div>

        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from');?>
                    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/parts/date_picker_range.tpl.php')); ?>
                </label>
                <div class="row">
                    <div class="col-md-12">
                        <input type="text" class="form-control form-control-sm" name="timefrom" id="id_timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($inputCanned->timefrom)?>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute from');?> <small>[<?php echo date('H:i:s')?>]</small></label>
                <div class="row">
                    <div class="col-md-4">
                        <select name="timefrom_hours" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($inputCanned->timefrom_hours) && $inputCanned->timefrom_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="timefrom_minutes" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($inputCanned->timefrom_minutes) && $inputCanned->timefrom_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="timefrom_seconds" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($inputCanned->timefrom_seconds) && $inputCanned->timefrom_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
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
                        <input type="text" class="form-control form-control-sm" name="timeto" id="id_timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($inputCanned->timeto)?>" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Hour and minute to');?> <small>[<?php echo date('H:i:s')?>]</small></label>
                <div class="row">
                    <div class="col-md-4">
                        <select name="timeto_hours" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select hour');?></option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($inputCanned->timeto_hours) && $inputCanned->timeto_hours === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> h.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="timeto_minutes" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select minute');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($inputCanned->timeto_minutes) && $inputCanned->timeto_minutes === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> m.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="timeto_seconds" class="form-control form-control-sm">
                            <option value=""><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Select seconds');?></option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <option value="<?php echo $i?>" <?php if (isset($inputCanned->timeto_seconds) && $inputCanned->timeto_seconds === $i) : ?>selected="selected"<?php endif;?>><?php echo str_pad($i,2, '0', STR_PAD_LEFT);?> s.</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-4">
            <div class="btn-group">
                <input type="submit" name="doSearchActions" class="btn btn-secondary d-block btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" /><a class="btn btn-outline-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('user/edit')?>/<?php echo $user->id;?>/(tab)/opactions"><span class="material-icons">refresh</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Reset');?></a>
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

<?php

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('user/edit') . '/' . $user->id . '/(tab)/opactions' . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParamsCanned['input_form']);
$pages->items_total = erLhAbstractModelAudit::getCount(array_merge_recursive($filterParamsCanned['filter'],array('filter' => array('user_id' => $user->id))));
$pages->setItemsPerPage(20);
$pages->paginate();

$cannedMessages = array();
if ($pages->items_total > 0) {
    $cannedMessages = erLhAbstractModelAudit::getList(array_merge_recursive($filterParamsCanned['filter'],array('filter' => array('user_id' => $user->id),'offset' => $pages->low, 'limit' => $pages->items_per_page)));
}

?>

<table class="table table-condensed table-small" cellpadding="0" cellspacing="0" ng-non-bindable>
    <thead>
    <tr>
        <th width="1%" nowrap="">[Record ID]</th>
        <th width="1%" nowrap="">[Object ID]</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Category');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Time');?></th>
    </tr>
    </thead>
    <?php foreach ($cannedMessages as $message) : ?>
        <tr>
            <td><?php echo $message->id?></td>
            <td><?php echo $message->object_id?></td>
            <td><?php echo htmlspecialchars($message->category)?>
            </td>
            <td>
                <?php echo htmlspecialchars($message->message)?>
            </td>
            <td>
                <?php echo htmlspecialchars($message->time)?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>
