<div ng-controller="TrItemCtrl as cmsg" ng-cloak ng-init='cmsg.languages = <?php echo json_encode($item->translation_array['items'],JSON_HEX_APOS)?>;cmsg.dialects = <?php echo json_encode(array_values(erLhcoreClassModelSpeechLanguageDialect::getDialectsGrouped()))?>'>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Group');?></label>
    <?php
    $params = array (
        'input_name'     => 'group_id',
        'display_name'   => 'name',
        'css_class'      => 'form-control',
        'selected_id'    => $item->group_id,
        'list_function'  => 'erLhcoreClassModelGenericBotTrGroup::getList',
        'list_function_params'  => array('limit' => false)
    );
    $params['optional_field'] = erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Choose');
    echo erLhcoreClassRenderHelper::renderCombobox( $params ); ?>
</div>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Identifier');?></label>
    <input type="text" class="form-control" name="identifier"  value="<?php echo htmlspecialchars($item->identifier);?>" />
</div>

<div role="tabpanel">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs mb-2" role="tablist" id="autoresponder-tabs">
        <li role="presentation" class="nav-item"><a class="nav-link active" href="#defaulttr" aria-controls="defaulttr" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Default');?></a></li>
        <li ng-repeat="lang in cmsg.languages" class="nav-item" role="presentation"><a class="nav-link" href="#lang-{{$index}}" aria-controls="lang-{{$index}}" role="tab" data-toggle="tab" ><i class="material-icons mr-0">&#xE894;</i> [{{cmsg.getLanguagesChecked(lang)}}]</a></li>
        <li class="nav-item"><a class="nav-link" href="#addlanguage" ng-click="cmsg.addLanguage()"><i class="material-icons">&#xE145;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Add translation');?></a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="defaulttr">
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Translation');?></label>

                <?php $bbcodeOptions = array('selector' => '#default-message-translation'); ?>
                <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>

                <textarea class="form-control form-control-sm" id="default-message-translation" name="default_message"><?php echo htmlspecialchars($item->translation_array['default']);?></textarea>

            </div>
        </div>
        <div ng-repeat="lang in cmsg.languages" role="tabpanel" class="tab-pane" id="lang-{{$index}}">

            <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/language_choose.tpl.php'));?>

            <div class="form-group">
                <label>Message</label>
                <?php $bbcodeOptions = array('selector' => '#message_{{$index}}'); ?>
                <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                <textarea class="form-control" id="message_{{$index}}" ng-model="lang.message" name="message_item[{{$index}}]"></textarea>
            </div>
        </div>
    </div>
</div>

</div>