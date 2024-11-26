<?php

if (isset($_GET['doSearchCanned'])) {
    $filterParamsCanned = erLhcoreClassSearchHandler::getParams(array(
            'module' => 'chat',
            'module_file' => 'canned_search',
            'format_filter' => true,
            'use_override' => true,
            'uparams' => $paramsRequest['user_parameters_unordered']));
} else {
    $filterParamsCanned = erLhcoreClassSearchHandler::getParams(array('module' => 'chat','module_file' => 'canned_search','format_filter' => true, 'uparams' => $paramsRequest['user_parameters_unordered']));
}

$inputCanned = $filterParamsCanned['input_form'];

?>

<form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>/(tab)/canned" method="get" ng-non-bindable>
    <input type="hidden" name="doSearchCanned" value="1">
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Title');?></label>
                <input type="text" class="form-control form-control-sm" name="title" value="<?php echo htmlspecialchars($inputCanned->title)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Message');?></label>
                <input type="text" class="form-control form-control-sm" name="message" value="<?php echo htmlspecialchars($inputCanned->message)?>" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Fallback message');?></label>
                <input type="text" class="form-control form-control-sm" name="fmsg" value="<?php echo htmlspecialchars($inputCanned->fmsg)?>" />
            </div>
        </div>
        <div class="col-md-4">
            <div class="btn-group pt-4">
                <input type="submit" name="doSearchCanned" class="btn btn-secondary d-block btn-sm" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" /><a class="btn btn-outline-secondary btn-sm" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>/(tab)/canned"><span class="material-icons">refresh</span><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Reset');?></a>
            </div>
        </div>
    </div>
</form>

<?php

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('user/account').'/(tab)/canned' . erLhcoreClassSearchHandler::getURLAppendFromInput($filterParamsCanned['input_form']);
$pages->items_total = erLhcoreClassModelCannedMsg::getCount(array_merge_recursive($filterParamsCanned['filter'],array('filter' => array('user_id' => $user->id))));
$pages->setItemsPerPage(20);
$pages->paginate();

$cannedMessages = array();
if ($pages->items_total > 0) {
    $cannedMessages = erLhcoreClassModelCannedMsg::getList(array_merge_recursive($filterParamsCanned['filter'],array('filter' => array('user_id' => $user->id),'offset' => $pages->low, 'limit' => $pages->items_per_page)));
}

?>

<table class="table" cellpadding="0" cellspacing="0" ng-non-bindable>
<thead>
<tr>
    <th width="1%">ID</th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Title/Message');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delay');?></th>
    <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?></th>    
    <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/cannedmsg/custom_column_multiinclude.tpl.php'));?>    
    <th width="1%">&nbsp;</th>
    <th width="1%">&nbsp;</th>
</tr>
</thead>
<?php foreach ($cannedMessages as $message) : ?>
    <tr>
        <td><?php echo $message->id?></td>
        <td><?php echo nl2br(htmlspecialchars($message->title != '' ? $message->title : $message->msg))?></td>
        <td><?php echo $message->delay?></td>
        <td><?php echo $message->position?></td>
        <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/cannedmsg/custom_column_content_multiinclude.tpl.php'));?>
        <td nowrap><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>/(msg)/<?php echo $message->id?>/(tab)/canned"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit message');?></a></td>
        <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>/(action)/delete/(tab)/canned/(msg)/<?php echo $message->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delete message');?></a></td>
    </tr>
<?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<hr>

<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Personal canned message');?></h3>

<?php if (isset($errors_canned)) : $errors = $errors_canned; ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated_canned)) : ?>
		<?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned message was saved'); ?>
		<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php if ($canned_msg->languages != '') : ?>
<script>
    var languageCanned<?php echo ($canned_msg->id > 0 ? $canned_msg->id : 0)?> = <?php echo $canned_msg->languages?>;
</script>
<?php endif; ?>

<script>
    var languageDialects = <?php echo json_encode(array_values(erLhcoreClassModelSpeechLanguageDialect::getDialectsGrouped()))?>;
</script>

<form action="<?php if ($canned_msg->id > 0) : ?><?php echo erLhcoreClassDesign::baseurl('user/account')?>/(tab)/canned/(msg)/<?php echo $canned_msg->id?><?php endif;?>#canned" method="post">

    <ul class="nav nav-pills" role="tablist" id="languageCanned-tabs">
        <li class="nav-item" role="presentation" ><a class="nav-link active" href="#main" aria-controls="main" role="tab" data-bs-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Main');?></a></li>

        <lhc-multilanguage-tab identifier="languageCanned" <?php if ($canned_msg->languages != '') : ?>init_langauges="<?php echo ($canned_msg->id > 0 ? $canned_msg->id : 0)?>"<?php endif;?>></lhc-multilanguage-tab>

    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="main">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Title');?></label>
                <input type="text" ng-non-bindable class="form-control" name="Title" value="<?php echo htmlspecialchars($canned_msg->title);?>" />
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Explain');?></label>
                <input type="text" ng-non-bindable class="form-control" name="ExplainHover" value="<?php echo htmlspecialchars($canned_msg->explain);?>" />
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("chat/cannedmsg","Tag's");?></label>
                <input type="text" ng-non-bindable class="form-control" name="Tags" value="<?php echo htmlspecialchars($canned_msg->tags_plain)?>" />
            </div>

            <div class="form-group">
                <label><input type="checkbox" name="AutoSend" value="on" <?php $canned_msg->auto_send == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Automatically send this message to user then chat is accepted');?></label>
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delay in seconds');?></label>
                <input type="text" ng-non-bindable class="form-control" name="Delay" value="<?php echo $canned_msg->delay?>" />
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?></label>
                <input type="text" ng-non-bindable class="form-control" name="Position" value="<?php echo $canned_msg->position?>" />
            </div>

            <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/cannedmsg/custom_fields_multiinclude.tpl.php'));?>

            <ul class="nav nav-pills" role="tablist" id="canned-main-extension">
                <li role="presentation" class="nav-item"><a class="nav-link active" href="#main-extension" aria-controls="main-extension" role="tab" data-bs-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Messages');?></a></li>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_tab_multiinclude.tpl.php')); ?>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="main-extension">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?>*</label>
                        <?php $bbcodeOptions = array('selector' => '#canned-message'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                        <textarea class="form-control" ng-non-bindable rows="5" name="Message" id="canned-message"><?php echo htmlspecialchars($canned_msg->msg);?></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Fallback message');?></label>
                        <?php $bbcodeOptions = array('selector' => '#id-FallbackMessage'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                        <textarea class="form-control" ng-non-bindable rows="5" name="FallbackMessage" id="id-FallbackMessage"><?php echo htmlspecialchars($canned_msg->fallback_msg);?></textarea>
                    </div>
                </div>
                <?php $canned_message = $canned_msg; ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_tab_content_multiinclude.tpl.php')); ?>
            </div>

        </div>

        <script>
            window.languageCannedFields = <?php echo json_encode([
                [
                    'name' => 'message_lang',
                    'bind_name' => 'message',
                    'name_literal' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message')
                ],
                [
                    'name' => 'fallback_message_lang',
                    'bind_name' => 'fallback_message',
                    'name_literal' => erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Fallback message')
                ]
            ])?>;
               $('.btn-block-department').makeDropdown();
          </script>

        <lhc-multilanguage-tab-content identifier="languageCanned" <?php if ($canned_msg->languages != '') : ?>init_langauges="<?php echo ($canned_msg->id > 0 ? $canned_msg->id : 0)?>"<?php endif;?>></lhc-multilanguage-tab-content>

    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	<div class="btn-group" role="group" aria-label="...">
		<input type="submit" class="btn btn-secondary" name="Save_canned_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
    	<?php if ($canned_msg->id > 0) : ?>
    	<input type="submit" class="btn btn-secondary" name="Cancel_canned_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    	<?php endif;?>
	</div>
	
</form>