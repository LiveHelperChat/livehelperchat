<div class="row mb-1">
    <div class="col-1"><a class="btn btn-sm btn-danger d-block" ng-click="cmsg.deleteLanguage(lang)"><i class="material-icons mr-0">&#xE15B;</i></a></div>
    <div class="col-11"><input type="text" ng-init="cmsg.query = lang.languages.length == 0 ? '*' : ''" ng-model="cmsg.query" value="" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Search for language. Enter * to see all.');?>" class="form-control form-control-sm"></div>
</div>

<div class="form-group">
    <div class="row" style="max-height: 200px;overflow-y: scroll">
        <div class="col-3" ng-repeat="langDialtect in cmsg.dialects" ng-init="cmsg.isSelectedDialect(lang,langDialtect)" ng-show="cmsg.query == '*' || (lang.dialect[langDialtect.lang.id] && cmsg.query == '') || (cmsg.query != '' && langDialtect.lang.name.toLowerCase().includes(cmsg.query.toLowerCase()) === true)">
            <div>
                <label class="fs12 mb-0"><input type="checkbox" value="" ng-model="lang.dialect[langDialtect.lang.id]" ng-click="cmsg.changeSelection(lang,langDialtect)">{{langDialtect.lang.name}}</label>
                <a ng-click="formDataLang['lang-show-'+langDialtect.lang.id] = !formDataLang['lang-show-'+langDialtect.lang.id]" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','See all variations');?>"><i class="material-icons mr-0">list</i></a>
            </div>
            <div id="lang-content-{{langDialtect.lang.id}}" ng-repeat="langDialtectItem in langDialtect.items" ng-show="formDataLang['lang-show-'+langDialtect.lang.id]">
                <label class="fs12 mb-0">
                    <input name="languages[{{$parent.$parent.$index}}][]" type="checkbox" value="{{langDialtectItem.lang_code}}" ng-checked="lang.languages.indexOf(langDialtectItem.lang_code) > -1" ng-click="cmsg.toggleSelection(lang,langDialtectItem.lang_code)"> {{langDialtectItem.lang_name}} [{{langDialtectItem.lang_code}}]
                </label>
                <br ng-if="langDialtectItem.short_code"/>
                <label class="fs12  mb-0" ng-if="langDialtectItem.short_code">
                    <input name="languages[{{$parent.$parent.$parent.$index}}][]" type="checkbox" value="{{langDialtectItem.short_code}}" ng-checked="lang.languages.indexOf(langDialtectItem.short_code) > -1" ng-click="cmsg.toggleSelection(lang,langDialtectItem.short_code)"> {{langDialtectItem.lang_name}} [{{langDialtectItem.short_code}}]
                </label>
                <br/>
            </div>
        </div>
    </div>
</div>