<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','If chat was accepted by the same language speaking operator you can send visitor a custom message on chat accept event.')?></p>

<div class="form-group">
    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','What languages should be ignored. If chat language is one of the selected, message will not be send.')?></label>

    <div class="row mb-1">
        <div class="col-12"><input type="text" ng-init="cmsg.query = cmsg.ignoreLanguages.languages.length == 0 ? '*' : ''" ng-model="cmsg.query" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Search for language. Enter * to see all.');?>" class="form-control form-control-sm"></div>
    </div>

    <div class="form-group">
        <div class="row" style="max-height: 200px;overflow-y: scroll">
            <div class="col-3" ng-repeat="langDialtect in cmsg.dialects" ng-init="cmsg.isSelectedDialect(cmsg.ignoreLanguages,langDialtect)" ng-show="cmsg.query == '*' || (cmsg.ignoreLanguages.dialect[langDialtect.lang.id] && cmsg.query == '') || (cmsg.query != '' && langDialtect.lang.name.toLowerCase().includes(cmsg.query.toLowerCase()) === true)">
                <div>
                    <label class="fs12 mb-0"><input type="checkbox" value="" ng-model="cmsg.ignoreLanguages.dialect[langDialtect.lang.id]" ng-click="cmsg.changeSelection(cmsg.ignoreLanguages,langDialtect)">{{langDialtect.lang.name}}</label>
                    <a ng-click="formDataLang['lang-show-'+langDialtect.lang.id] = !formDataLang['lang-show-'+langDialtect.lang.id]" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','See all variations');?>"><i class="material-icons mr-0">list</i></a>
                </div>
                <div id="lang-content-{{langDialtect.lang.id}}" ng-repeat="langDialtectItem in langDialtect.items" ng-show="formDataLang['lang-show-'+langDialtect.lang.id]">
                    <label class="fs12 mb-0">
                        <input name="languages_ignore[]" type="checkbox" value="{{langDialtectItem.lang_code}}" ng-checked="cmsg.ignoreLanguages.languages.indexOf(langDialtectItem.lang_code) > -1" ng-click="cmsg.toggleSelection(cmsg.ignoreLanguages,langDialtectItem.lang_code)"> {{langDialtectItem.lang_name}} [{{langDialtectItem.lang_code}}]
                    </label>
                    <br ng-if="langDialtectItem.short_code"/>
                    <label class="fs12  mb-0" ng-if="langDialtectItem.short_code">
                        <input name="languages_ignore[]" type="checkbox" value="{{langDialtectItem.short_code}}" ng-checked="cmsg.ignoreLanguages.languages.indexOf(langDialtectItem.short_code) > -1" ng-click="cmsg.toggleSelection(cmsg.ignoreLanguages,langDialtectItem.short_code)"> {{langDialtectItem.lang_name}} [{{langDialtectItem.short_code}}]
                    </label>
                    <br/>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="form-group">
    <label><?php echo $fields['multilanguage_message']['trans'];?> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/proactivechatinvitation','If you leave empty - message we will be send only if translated message is found.')?></label>
    <?php $bbcodeOptions = array('selector' => 'textarea[name=AbstractInput_multilanguage_message]'); ?>
    <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
    <?php echo erLhcoreClassAbstract::renderInput('multilanguage_message', $fields['multilanguage_message'], $object)?>
</div>