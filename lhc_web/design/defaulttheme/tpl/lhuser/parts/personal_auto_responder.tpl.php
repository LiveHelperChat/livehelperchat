<?php
$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('user/account').'/(tab)/autoresponder';
$pages->items_total = erLhAbstractModelAutoResponder::getCount(array('filter' => array('user_id' => $user->id)));
$pages->setItemsPerPage(10);
$pages->paginate();

$autoResponderMessages = array();
if ($pages->items_total > 0) {
    $autoResponderMessages = erLhAbstractModelAutoResponder::getList(array('filter' => array('user_id' => $user->id),'offset' => $pages->low, 'limit' => $pages->items_per_page,'sort' => 'id ASC'));
}

?>

<table class="table" cellpadding="0" cellspacing="0">
    <thead>
    <tr>
        <th width="1%">ID</th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Name');?></th>
        <th><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Department');?></th>
        <th width="1%">&nbsp;</th>
        <th width="1%">&nbsp;</th>
    </tr>
    </thead>
    <?php foreach ($autoResponderMessages as $autoResponderMessage) : ?>
        <tr>
            <td><?php echo $autoResponderMessage->id?></td>
            <td><?php echo nl2br(htmlspecialchars($autoResponderMessage->name))?></td>
            <td><?php echo nl2br(htmlspecialchars((string)$autoResponderMessage->dep))?></td>
            <td nowrap><a class="btn btn-secondary btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>/(msg)/<?php echo $autoResponderMessage->id?>/(tab)/autoresponder"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Edit message');?></a></td>
            <td nowrap><a onclick="return confirm('<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('kernel/message','Are you sure?');?>')" class="csfr-required btn btn-danger btn-xs" href="<?php echo erLhcoreClassDesign::baseurl('user/account')?>/(action)/delete/(tab)/autoresponder/(msg)/<?php echo $autoResponderMessage->id?>"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delete message');?></a></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/secure_links.tpl.php')); ?>

<?php if (isset($pages)) : ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/paginator.tpl.php')); ?>
<?php endif;?>

<hr>

<h3><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Personal auto responder message');?></h3>

<?php if (isset($errors_autoresponder)) : $errors = $errors_autoresponder; ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated_autoresponder)) : ?>
    <?php $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Canned message was saved'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<?php $fields = $autoResponder_msg->getFields(); $object = $autoResponder_msg;?>
<form action="<?php echo erLhcoreClassDesign::baseurl('user/account')?>/(tab)/autoresponder<?php if ($autoResponder_msg->id > 0) : ?>/(msg)/<?php echo $autoResponder_msg->id?><?php endif;?>#autoresponder" method="post" ng-controller="AutoResponderCtrl as cmsg" ng-cloak  ng-init='<?php if ($autoResponder_msg->languages != '') : ?>cmsg.languages = <?php echo json_encode(json_decode($autoResponder_msg->languages,true),JSON_HEX_APOS)?>;<?php endif;?>cmsg.dialects = <?php echo json_encode(array_values(erLhcoreClassModelSpeechLanguageDialect::getDialectsGrouped()))?>'>

    <div class="form-group">
        <label><?php echo $fields['name']['trans'];?></label>
        <?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $autoResponder_msg)?>
    </div>

    <div class="form-group">
        <label><?php echo $fields['position']['trans'];?></label>
        <?php echo erLhcoreClassAbstract::renderInput('position', $fields['position'], $autoResponder_msg)?>
    </div>

    <div class="form-group">
        <label><?php echo $fields['dep_id']['trans'];?></label>
        <?php echo erLhcoreClassAbstract::renderInput('dep_id', $fields['dep_id'], $autoResponder_msg)?>
    </div>

    <div role="tabpanel">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs mb-2" role="tablist" id="autoresponder-tabs">
            <li role="presentation" class="nav-item"><a class="nav-link active" href="#active" aria-controls="active" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Visitor not replying messaging');?></a></li>
            <li role="presentation" class="nav-item"><a class="nav-link" href="#operatornotreply" aria-controls="active" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator not replying messaging');?></a></li>
            <li role="presentation" class="nav-item"><a class="nav-link" href="#onhold" aria-controls="onhold" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','On-hold chat messaging');?></a></li>

            <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','personalautoresponder_cm')) : ?>
            <li role="presentation" class="nav-item"><a class="nav-link" href="#closeaction" aria-controls="closeaction" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Close messaging');?></a></li>
            <?php endif; ?>

            <li ng-repeat="lang in cmsg.languages" class="nav-item" role="presentation"><a class="nav-link" href="#lang-{{$index}}" aria-controls="lang-{{$index}}" role="tab" data-toggle="tab" ><i class="material-icons mr-0">&#xE894;</i> [{{cmsg.getLanguagesChecked(lang)}}]</a></li>
            <li class="nav-item"><a class="nav-link" href="#addlanguage" ng-click="cmsg.addLanguage()"><i class="material-icons">&#xE145;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Add translation');?></a></li>
        </ul>

        <?php $autoResponderOptions = array(
            'hide_pending' => true,
            'hide_wait_message' => true,
            'hide_operator_nick' => true,

            'hide_visitor_not_replying_timeout' => true,
            'hide_visitor_not_replying_bot' => true,

            'hide_operator_not_replying_timeout' => true,
            'hide_operator_not_replying_bot' => true,

            'hide_on_hold_timeout' => true,
            'hide_on_hold_bot' => true,

            'hide_personal_closing' => !erLhcoreClassUser::instance()->hasAccessTo('lhuser','personalautoresponder_cm')
        ); ?>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="active">
                <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/responder/active.tpl.php'));?>
            </div>
            <div role="tabpanel" class="tab-pane" id="operatornotreply">
                <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/responder/operatornotreply.tpl.php'));?>
            </div>
            <div role="tabpanel" class="tab-pane" id="onhold">
                <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/responder/onhold.tpl.php'));?>
            </div>

            <?php if (erLhcoreClassUser::instance()->hasAccessTo('lhuser','personalautoresponder_cm')) : ?>
            <div role="tabpanel" class="tab-pane" id="closeaction">
                <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/responder/closeaction.tpl.php'));?>
            </div>
            <?php endif; ?>

            <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/responder/languages.tpl.php'));?>
        </div>
    </div>

    <?php include(erLhcoreClassDesign::designtpl('lhkernel/csfr_token.tpl.php'));?>

    <div class="btn-group" role="group" aria-label="...">
        <input type="submit" class="btn btn-secondary" name="Save_autoresponder_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
        <?php if ($autoResponder_msg->id > 0) : ?>
            <input type="submit" class="btn btn-secondary" name="Cancel_autoresponder_action" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
        <?php endif;?>
    </div>

</form>

<script>

    $('select[name*="AbstractInput_nreply_op_bot_id"],select[name="AbstractInput_nreply_op_bot_id_1"],select[name="AbstractInput_pending_bot_id"],select[name="AbstractInput_nreply_bot_id"],select[name="AbstractInput_onhold_bot_id"]').change(function(){
        var identifier = $(this).attr('name').replace(/AbstractInput_|_bot_id/g,"");
        $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $(this).val() + '/0/(preview)/1/(element)/'+identifier+'_trigger_id', { }, function(data) {
            $('#'+identifier+'-trigger-list-id').html(data);
            renderPreview($('select[name="AbstractInput_'+identifier+'_trigger_id"]'));
        }).fail(function() {

        });
    });

    var responderItems = [{'id':'pending_bot_id','val' : <?php echo (isset($autoResponder_msg->bot_configuration_array['pending_trigger_id'])) ? $autoResponder_msg->bot_configuration_array['pending_trigger_id'] : 0 ?>},{'id':'nreply_bot_id','val':<?php echo (isset($autoResponder_msg->bot_configuration_array['nreply_trigger_id'])) ? $autoResponder_msg->bot_configuration_array['nreply_trigger_id'] : 0 ?>},{'id':'onhold_bot_id','val': <?php echo (isset($autoResponder_msg->bot_configuration_array['onhold_trigger_id'])) ? $autoResponder_msg->bot_configuration_array['onhold_trigger_id'] : 0 ?>}];

    <?php for ($i = 1; $i <= 5; $i++)  : ?>
    responderItems.push({'id':'nreply_op_bot_id_<?php echo $i?>','val' : <?php echo (isset($autoResponder_msg->bot_configuration_array['nreply_op_' . $i .'_trigger_id'])) ? $autoResponder_msg->bot_configuration_array['nreply_op_' . $i .'_trigger_id'] : 0 ?>});
    <?php endfor; ?>

    $.each(responderItems, function( index, value ) {
        var identifier = value.id.replace(/AbstractInput_|_bot_id/g,"");
        $.get(WWW_DIR_JAVASCRIPT + 'genericbot/triggersbybot/' + $('select[name="AbstractInput_'+value.id+'"]').val() + '/'+value.val+'/(preview)/1/(element)/'+identifier+'_trigger_id', { }, function(data) {
            $('#' + identifier +'-trigger-list-id').html(data);
            if (parseInt(value.val) > 0){
                renderPreview($('select[name="AbstractInput_' + identifier +'_trigger_id"]'));
            }
        }).fail(function() {

        });
    });

    function renderPreview(inst) {
        var identifier = inst.attr('name').replace(/AbstractInput_|_trigger_id/g,"");
        $.get(WWW_DIR_JAVASCRIPT + 'theme/renderpreview/' + inst.val(), { }, function(data) {
            $('#'+identifier+'-trigger-preview-window').html(data);
        }).fail(function() {
            $('#'+identifier+'-trigger-preview-window').html('');
        });
    }
</script>