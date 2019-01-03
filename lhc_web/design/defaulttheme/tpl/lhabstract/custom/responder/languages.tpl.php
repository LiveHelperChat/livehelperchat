<div ng-repeat="lang in cmsg.languages" role="tabpanel" class="tab-pane" id="lang-{{$index}}">

    <a class="btn btn-xs btn-danger" ng-click="cmsg.deleteLanguage(lang)"><i class="material-icons mr-0">&#xE15B;</i></a>
    <br>
    <div class="form-group">
        <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Language');?>*</label>
        <div class="row">
            <div class="col-3" ng-repeat="langDialtect in cmsg.dialects">
                <label class="fs12">
                    <input name="languages[{{$parent.$index}}][]" type="checkbox" value="{{langDialtect.lang_code}}" ng-checked="lang.languages.indexOf(langDialtect.lang_code) > -1" ng-click="cmsg.toggleSelection(lang,langDialtect.lang_code)"> {{langDialtect.lang_name}} [{{langDialtect.lang_code}}]
                </label>
                <br ng-if="langDialtect.short_code"/>
                <label class="fs12" ng-if="langDialtect.short_code">
                    <input name="languages[{{$parent.$index}}][]" type="checkbox" value="{{langDialtect.short_code}}" ng-checked="lang.languages.indexOf(langDialtect.short_code) > -1" ng-click="cmsg.toggleSelection(lang,langDialtect.short_code)"> {{langDialtect.lang_name}} [{{langDialtect.short_code}}]
                </label>
                <br/>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label><?php echo $fields['wait_message']['trans'];?></label>
        <input type="text" class="form-control" ng-model="lang.wait_message" value="" name="wait_message[{{$index}}]">
    </div>

    <div class="form-group">
        <label><?php echo $fields['operator']['trans'];?></label>
        <input type="text" class="form-control" ng-model="lang.operator" value="" name="operator[{{$index}}]">
    </div>

    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Pending chat messaging');?></h4>

    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label><?php echo $fields['timeout_message']['trans'];?> [1]</label>
                <input type="text" class="form-control" ng-model="lang.timeout_message" value="" name="timeout_message[{{$index}}]">
            </div>
        </div>
        <?php for ($i = 2; $i <= 5; $i++) : ?>
        <div class="col-6">
            <div class="form-group">
                <label><?php echo $fields['timeout_message_' . $i]['trans'];?> [<?php echo $i?>]</label>
                <input type="text" class="form-control" ng-model="lang.timeout_message_<?php echo $i?>" value="" name="timeout_message_<?php echo $i?>[{{$index}}]">
            </div>
        </div>
        <?php endfor;?>
    </div>

    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Not replying messaging');?></h4>

    <div class="row">
    <?php for ($i = 1; $i <= 5; $i++) : ?>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo $fields['timeout_reply_message_' . $i]['trans'];?></label>
                    <input type="text" class="form-control" ng-model="lang.timeout_reply_message_<?php echo $i?>" value="" name="timeout_reply_message_<?php echo $i?>[{{$index}}]">
                 </div>
            </div>
    <?php endfor;?>
    </div>

    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','On-hold chat messaging');?></h4>

    <div class="form-group">
        <label><?php echo $fields['wait_timeout_hold']['trans'];?></label>
        <input type="text" class="form-control" ng-model="lang.wait_timeout_hold" value="" name="wait_timeout_hold[{{$index}}]">
    </div>

    <div class="row">
    <?php for ($i = 1; $i <= 5; $i++) : ?>
        <div class="col-6">
            <div class="form-group">
                <label><?php echo $fields['timeout_hold_message_' . $i]['trans'];?></label>
                <input type="text" class="form-control" ng-model="lang.timeout_hold_message_<?php echo $i?>" value="" name="timeout_hold_message_<?php echo $i?>[{{$index}}]">
            </div>
        </div>
    <?php endfor;?>
    </div>
</div>