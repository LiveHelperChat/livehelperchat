<?php $mainAttribute = isset($fields[$translatableItem['identifier']]['main_attr']) ? 'main_attr' : 'main_attr_lang'; ?>
<div class="mt-2" ng-controller="ThemeAttrTranslatableCtrl as attrTranslatable" ng-init='attrTranslatable.identifier = "<?php echo $translatableItem['identifier']?>";<?php if (isset($object->{$fields[$translatableItem['identifier']][$mainAttribute]}[$translatableItem['identifier'] . '_lang'])) : ?>attrTranslatable.languages = <?php echo json_encode($object->{$fields[$translatableItem['identifier']][$mainAttribute]}[$translatableItem['identifier'] . '_lang'],JSON_HEX_APOS)?>;<?php endif;?>attrTranslatable.dialects = <?php echo json_encode(array_values(erLhcoreClassModelSpeechLanguageDialect::getDialectsGrouped()))?>'>
    <ul class="nav nav-tabs" role="tablist" id="translate-tabs-<?php echo $translatableItem['identifier']?>">
        <li role="presentation" class="nav-item" ><a class="nav-link active" href="#main-<?php echo $translatableItem['identifier']?>" aria-controls="main-<?php echo $translatableItem['identifier']?>" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Main');?></a></li>
        <li ng-repeat="lang in attrTranslatable.languages" class="nav-item" role="presentation"><a href="#lang-<?php echo $translatableItem['identifier']?>-{{$index}}" class="nav-link" aria-controls="lang-<?php echo $translatableItem['identifier']?>-{{$index}}" role="tab" data-toggle="tab" ><i class="material-icons mr-0">&#xE894;</i> [{{attrTranslatable.getLanguagesChecked(lang)}}]</a></li>
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
                <?php echo erLhcoreClassAbstract::renderInput($translatableItem['identifier'], $fields[$translatableItem['identifier']], $object, (isset($translatableItem['default_value']) ? $translatableItem['default_value'] : ''))?>
            </div>
        </div>
        <div ng-repeat="lang in attrTranslatable.languages" role="tabpanel" class="tab-pane pt-2" id="lang-<?php echo $translatableItem['identifier']?>-{{$index}}">

            <div class="row mb-1">
                <div class="col-1"><a class="btn btn-sm btn-danger d-block" ng-click="attrTranslatable.deleteLanguage(lang)"><i class="material-icons mr-0">&#xE15B;</i></a></div>
                <div class="col-11"><input type="text" ng-init="attrTranslatable.query = lang.languages.length == 0 ? '*' : ''" ng-model="attrTranslatable.query" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Search for language. Enter * to see all.');?>" class="form-control form-control-sm"></div>
            </div>

            <div class="form-group">
                <div class="row" style="max-height: 200px;overflow-y: scroll">
                    <div class="col-3" ng-repeat="langDialtect in attrTranslatable.dialects" ng-init="attrTranslatable.isSelectedDialect(lang,langDialtect)" ng-show="attrTranslatable.query == '*' || (lang.dialect[langDialtect.lang.id] && attrTranslatable.query == '') || (attrTranslatable.query != '' && langDialtect.lang.name.toLowerCase().includes(attrTranslatable.query.toLowerCase()) === true)">
                        <div>
                            <label class="fs12 mb-0"><input type="checkbox" value="" ng-model="lang.dialect[langDialtect.lang.id]" ng-click="attrTranslatable.changeSelection(lang,langDialtect)">{{langDialtect.lang.name}}</label>
                            <a title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','See all variations');?>" ng-click="formDataLang['lang-show-'+langDialtect.lang.id] = !formDataLang['lang-show-'+langDialtect.lang.id]"><i class="material-icons mr-0">list</i></a>
                        </div>
                        <div id="lang-content-<?php echo $translatableItem['identifier']?>-{{langDialtect.lang.id}}" ng-repeat="langDialtectItem in langDialtect.items" ng-show="formDataLang['lang-show-'+langDialtect.lang.id]">
                            <label class="fs12 mb-0">
                                <input name="AbstractInput_<?php echo $translatableItem['identifier']?>_languages[{{$parent.$parent.$index}}][]" type="checkbox" value="{{langDialtectItem.lang_code}}" ng-checked="lang.languages.indexOf(langDialtectItem.lang_code) > -1" ng-click="attrTranslatable.toggleSelection(lang,langDialtectItem.lang_code)"> {{langDialtectItem.lang_name}} [{{langDialtectItem.lang_code}}]
                            </label>
                            <br ng-if="langDialtectItem.short_code"/>
                            <label class="fs12  mb-0" ng-if="langDialtectItem.short_code">
                                <input name="AbstractInput_<?php echo $translatableItem['identifier']?>_languages[{{$parent.$parent.$parent.$index}}][]" type="checkbox" value="{{langDialtectItem.short_code}}" ng-checked="lang.languages.indexOf(langDialtectItem.short_code) > -1" ng-click="attrTranslatable.toggleSelection(lang,langDialtectItem.short_code)"> {{langDialtectItem.lang_name}} [{{langDialtectItem.short_code}}]
                            </label>
                            <br/>
                        </div>
                    </div>
                </div>
            </div>
            <label><?php echo $fields[$translatableItem['identifier']]['trans'];?></label>
            <div class="form-group">
                <?php if ($fields[$translatableItem['identifier']]['type'] == 'textarea') : ?>
                    <?php if (isset($translatableItem['bb_code_selected'])) : ?>
                        <?php $bbcodeOptions = array('selector' => "#AbstractInput_" . $translatableItem['identifier'] . '_content_{{$index}}', 'index' => '{{$index}}'); ?>
                        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                    <?php endif; ?>
                    <textarea class="form-control" rows="5" <?php if (isset($fields[$translatableItem['identifier']]['placeholder'])) : ?>placeholder="<?php echo $fields[$translatableItem['identifier']]['placeholder']?>"<?php endif; ?> id="AbstractInput_<?php echo $translatableItem['identifier']?>_content_{{$index}}" name="AbstractInput_<?php echo $translatableItem['identifier']?>_content[{{$index}}]" ng-model="lang.content"></textarea>
                <?php else : ?>
                    <input type="text" class="form-control" <?php if (isset($fields[$translatableItem['identifier']]['placeholder'])) : ?>placeholder="<?php echo $fields[$translatableItem['identifier']]['placeholder']?>"<?php endif; ?> name="AbstractInput_<?php echo $translatableItem['identifier']?>_content[{{$index}}]" ng-model="lang.content" />
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
