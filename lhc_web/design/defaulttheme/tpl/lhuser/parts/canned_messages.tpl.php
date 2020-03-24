<?php 
$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('user/account').'/(tab)/canned';
$pages->items_total = erLhcoreClassModelCannedMsg::getCount(array('filter' => array('user_id' => $user->id)));
$pages->setItemsPerPage(10);
$pages->paginate();

$cannedMessages = array();
if ($pages->items_total > 0) {
    $cannedMessages = erLhcoreClassModelCannedMsg::getList(array('filter' => array('user_id' => $user->id),'offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'));
}

?>

<table class="table" cellpadding="0" cellspacing="0">
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

<form ng-controller="CannedMsgCtrl as cmsg"  ng-init='<?php if ($canned_msg->languages != '') : ?>cmsg.languages = <?php echo $canned_msg->languages?>;<?php endif;?>cmsg.dialects = <?php echo json_encode(array_values(erLhcoreClassModelSpeechLanguageDialect::getDialectsGrouped()))?>' action="<?php if ($canned_msg->id > 0) : ?><?php echo erLhcoreClassDesign::baseurl('user/account')?>/(tab)/canned/(msg)/<?php echo $canned_msg->id?><?php endif;?>#canned" method="post">

    <ul class="nav nav-pills" role="tablist" id="canned-main-tabs">
        <li class="nav-item" role="presentation" ><a class="nav-link active"href="#main" aria-controls="main" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Main');?></a></li>
        <li class="nav-item" ng-repeat="lang in cmsg.languages" role="presentation"><a class="nav-link" href="#lang-{{$index}}" aria-controls="lang-{{$index}}" role="tab" data-toggle="tab" ><i class="material-icons mr-0">&#xE894;</i> [{{cmsg.getLanguagesChecked(lang)}}]</a></li>
        <li class="nav-item" ><a class="nav-link" href="#addlanguage" ng-click="cmsg.addLanguage()"><i class="material-icons">&#xE145;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('user/account','Add translation');?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="main">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Title');?></label>
                <input type="text" class="form-control" name="Title" value="<?php echo htmlspecialchars($canned_msg->title);?>" />
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Explain');?></label>
                <input type="text" class="form-control" name="ExplainHover" value="<?php echo htmlspecialchars($canned_msg->explain);?>" />
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("chat/cannedmsg","Tag's");?></label>
                <input type="text" class="form-control" name="Tags" value="<?php echo htmlspecialchars($canned_msg->tags_plain)?>" />
            </div>

            <div class="form-group">
                <label><input type="checkbox" name="AutoSend" value="on" <?php $canned_msg->auto_send == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Automatically send this message to user then chat is accepted');?></label>
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delay in seconds');?></label>
                <input type="text" class="form-control" name="Delay" value="<?php echo $canned_msg->delay?>" />
            </div>

            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?></label>
                <input type="text" class="form-control" name="Position" value="<?php echo $canned_msg->position?>" />
            </div>

            <?php include(erLhcoreClassDesign::designtpl('lhuser/parts/cannedmsg/custom_fields_multiinclude.tpl.php'));?>

            <ul class="nav nav-pills" role="tablist" id="canned-main-extension">
                <li role="presentation" class="nav-item"><a class="nav-link active" href="#main-extension" aria-controls="main-extension" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Messages');?></a></li>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_tab_multiinclude.tpl.php')); ?>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="main-extension">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?>*</label>
                        <?php $bbcodeOptions = array('selector' => '#canned-message'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                        <textarea class="form-control" rows="5" name="Message" id="canned-message"><?php echo htmlspecialchars($canned_msg->msg);?></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Fallback message');?></label>
                        <?php $bbcodeOptions = array('selector' => '#id-FallbackMessage'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                        <textarea class="form-control" rows="5" name="FallbackMessage" id="id-FallbackMessage"><?php echo htmlspecialchars($canned_msg->fallback_msg);?></textarea>
                    </div>
                </div>
                <?php $canned_message = $canned_msg; ?>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_tab_content_multiinclude.tpl.php')); ?>
            </div>

        </div>

        <div ng-repeat="lang in cmsg.languages" role="tabpanel" class="tab-pane" id="lang-{{$index}}">

            <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/language_choose.tpl.php'));?>

            <ul class="nav nav-pills" role="tablist">
                <li role="presentation" class="nav-item"><a class="nav-link active" href="#main-extension-lang-{{$index}}" aria-controls="main-extension-lang-{{$index}}" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Messages');?></a></li>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_lang_tab_multiinclude.tpl.php')); ?>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="main-extension-lang-{{$index}}">
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?>*</label>
                        <?php $bbcodeOptions = array('selector' => '#message_lang-{{$index}}'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                        <textarea class="form-control" rows="5" id="message_lang-{{$index}}" name="message_lang[{{$index}}]" ng-model="lang.message"></textarea>
                    </div>
                    <div class="form-group">
                        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Fallback message');?></label>
                        <?php $bbcodeOptions = array('selector' => '#fallback_message_lang-{{$index}}'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                        <textarea class="form-control" rows="5" id="fallback_message_lang-{{$index}}" name="fallback_message_lang[{{$index}}]" ng-model="lang.fallback_message"></textarea>
                    </div>
                </div>
                <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_lang_tab_content_multiinclude.tpl.php')); ?>
            </div>

        </div>

    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

	<div class="btn-group" role="group" aria-label="...">
		<input type="submit" class="btn btn-secondary" name="Save_canned_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
    	<?php if ($canned_msg->id > 0) : ?>
    	<input type="submit" class="btn btn-secondary" name="Cancel_canned_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
    	<?php endif;?>
	</div>
	
</form>