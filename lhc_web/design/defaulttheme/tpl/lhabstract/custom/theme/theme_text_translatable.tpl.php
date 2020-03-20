<?php $mainAttribute = isset($fields[$translatableItem['identifier']]['main_attr']) ? 'main_attr' : 'main_attr_lang'; ?>
<div class="mt-2" ng-controller="ThemeAttrTranslatableCtrl as attrTranslatable" ng-init='attrTranslatable.identifier = "<?php echo $translatableItem['identifier']?>";<?php if (isset($object->{$fields[$translatableItem['identifier']][$mainAttribute]}[$translatableItem['identifier'] . '_lang'])) : ?>attrTranslatable.languages = <?php echo json_encode($object->{$fields[$translatableItem['identifier']][$mainAttribute]}[$translatableItem['identifier'] . '_lang'],JSON_HEX_APOS)?>;<?php endif;?>attrTranslatable.dialects = <?php echo json_encode(array_values(erLhcoreClassModelSpeechLanguageDialect::getList()))?>'>

    <ul class="nav nav-tabs" role="tablist" id="translate-tabs-<?php echo $translatableItem['identifier']?>">
        <li role="presentation" class="nav-item" ><a class="nav-link active" href="#main-<?php echo $translatableItem['identifier']?>" aria-controls="main-<?php echo $translatableItem['identifier']?>" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Main');?></a></li>
        <li ng-repeat="lang in attrTranslatable.languages" class="nav-item" role="presentation"><a href="#lang-<?php echo $translatableItem['identifier']?>-{{$index}}" class="nav-link" aria-controls="lang-<?php echo $translatableItem['identifier']?>-{{$index}}" role="tab" data-toggle="tab" ><i class="material-icons mr-0">&#xE894;</i></a></li>
        <li class="nav-item"><a href="#addlanguage" class="nav-link" ng-click="attrTranslatable.addLanguage()"><i class="material-icons">&#xE145;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Add translation');?></a></li>
    </ul>

    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="main-<?php echo $translatableItem['identifier']?>">
            <label><?php echo $fields[$translatableItem['identifier']]['trans'];?></label>
            <div class="form-group">

                <?php if (isset($translatableItem['bb_code_selected'])) : ?>
                    <?php $bbcodeOptions = array('selector' => $translatableItem['bb_code_selected']); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                <?php endif; ?>

                <?php echo erLhcoreClassAbstract::renderInput($translatableItem['identifier'], $fields[$translatableItem['identifier']], $object)?>
            </div>
        </div>
        <div ng-repeat="lang in attrTranslatable.languages" role="tabpanel" class="tab-pane pt-2" id="lang-<?php echo $translatableItem['identifier']?>-{{$index}}">
            <a class="btn btn-xs btn-danger" ng-click="attrTranslatable.deleteLanguage(lang)"><i class="material-icons mr-0">&#xE15B;</i></a>
            <br>
            <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Language');?>*</label>
                <div class="row" style="max-height: 200px;overflow-y: scroll">
                    <div class="col-3" ng-repeat="langDialtect in attrTranslatable.dialects">
                        <label class="fs12">
                            <input name="AbstractInput_<?php echo $translatableItem['identifier']?>_languages[{{$parent.$index}}][]" type="checkbox" value="{{langDialtect.lang_code}}" ng-checked="lang.languages.indexOf(langDialtect.lang_code) > -1" ng-click="attrTranslatable.toggleSelection(lang,langDialtect.lang_code)"> {{langDialtect.lang_name}} [{{langDialtect.lang_code}}]
                        </label>
                        <br ng-if="langDialtect.short_code"/>
                        <label class="fs12" ng-if="langDialtect.short_code">
                            <input name="AbstractInput_<?php echo $translatableItem['identifier']?>_languages[{{$parent.$parent.$index}}][]" type="checkbox" value="{{langDialtect.short_code}}" ng-checked="lang.languages.indexOf(langDialtect.short_code) > -1" ng-click="attrTranslatable.toggleSelection(lang,langDialtect.short_code)"> {{langDialtect.lang_name}} [{{langDialtect.short_code}}]
                        </label>
                        <br/>
                    </div>
                </div>
            </div>
            <label><?php echo $fields[$translatableItem['identifier']]['trans'];?></label>
            <div class="form-group">
                <?php if ($fields[$translatableItem['identifier']]['type'] == 'textarea') : ?>

                    <?php if (isset($translatableItem['bb_code_selected'])) : ?>
                        <?php $bbcodeOptions = array('selector' => "#AbstractInput_" . $translatableItem['identifier'] . '_content_{{index}}'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                    <?php endif; ?>

                    <textarea class="form-control" rows="5" <?php if (isset($fields[$translatableItem['identifier']]['placeholder'])) : ?>placeholder="<?php echo $fields[$translatableItem['identifier']]['placeholder']?>"<?php endif; ?> id="AbstractInput_<?php echo $translatableItem['identifier']?>_content_{{index}}" name="AbstractInput_<?php echo $translatableItem['identifier']?>_content[{{$index}}]" ng-model="lang.content"></textarea>
                <?php else : ?>
                    <input type="text" class="form-control" <?php if (isset($fields[$translatableItem['identifier']]['placeholder'])) : ?>placeholder="<?php echo $fields[$translatableItem['identifier']]['placeholder']?>"<?php endif; ?> name="AbstractInput_<?php echo $translatableItem['identifier']?>_content[{{$index}}]" ng-model="lang.content" />
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
