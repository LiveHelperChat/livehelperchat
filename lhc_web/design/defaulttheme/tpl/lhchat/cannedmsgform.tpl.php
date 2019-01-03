<div ng-controller="CannedMsgCtrl as cmsg"  ng-init='<?php if ($canned_message->languages != '') : ?>cmsg.languages = <?php echo $canned_message->languages?>;<?php endif;?>cmsg.dialects = <?php echo json_encode(array_values(erLhcoreClassModelSpeechLanguageDialect::getList()))?>'>

<ul class="nav nav-pills" role="tablist" id="canned-main-tabs">
    <li role="presentation" <?php if ( (isset($tab) && $tab == 'main') || !isset($tab)) : ?>class="active"<?php endif;?>><a href="#main" aria-controls="main" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Main');?></a></li>
    <li ng-repeat="lang in cmsg.languages" role="presentation"><a href="#lang-{{$index}}" aria-controls="lang-{{$index}}" role="tab" data-toggle="tab" ><i class="material-icons mr-0">&#xE894;</i></a></li>
    <li><a href="#addlanguage" ng-click="cmsg.addLanguage()"><i class="material-icons">&#xE145;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Add translation');?></a></li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane <?php if ($tab == '') : ?>active<?php endif;?>" id="main">

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Title');?></label>
            <input type="text" class="form-control" name="Title" value="<?php echo htmlspecialchars($canned_message->title);?>" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation("chat/cannedmsg","Tag's");?></label>
            <input type="text" class="form-control" name="Tags" value="<?php echo htmlspecialchars($canned_message->tags_plain)?>" />
        </div>

        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Explain');?></label>
            <input type="text" class="form-control" name="ExplainHover" value="<?php echo htmlspecialchars($canned_message->explain);?>" />
        </div>

        <label><input type="checkbox" name="AutoSend" value="on" <?php $canned_message->auto_send == 1 ? print 'checked="checked"' : ''?> /> <?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Automatically send this message to user then chat is accepted');?></label>

        <div class="form-group">
           <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Delay in seconds');?></label>
           <input type="text" class="form-control" name="Delay" value="<?php echo $canned_message->delay?>" />
        </div>

        <div class="form-group">
                <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Position');?>
                    <a class="live-help-tooltip" data-placement="top" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','The smaller the position value the higher the canned message will appear in the list')?>" data-toggle="tooltip" ><i class="material-icons">&#xE887;</i></a>
                </label>

            <input type="text" class="form-control" name="Position" value="<?php echo $canned_message->position?>" />
        </div>

        <?php $showAnyDepartment = true; ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/department.tpl.php')); ?>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/after_cannedmsgform_multiinclude.tpl.php')); ?>

        <ul class="nav nav-pills" role="tablist" id="canned-main-extension">
            <li role="presentation" class="active"><a href="#main-extension" aria-controls="main-extension" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Messages');?></a></li>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_tab_multiinclude.tpl.php')); ?>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="main-extension">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?>*</label>
                    <textarea class="form-control" rows="5" name="Message"><?php echo htmlspecialchars($canned_message->msg);?></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Fallback message');?></label>
                    <textarea class="form-control" rows="5" name="FallbackMessage"><?php echo htmlspecialchars($canned_message->fallback_msg);?></textarea>
                </div>
            </div>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_tab_content_multiinclude.tpl.php')); ?>
        </div>

    </div>

    <div ng-repeat="lang in cmsg.languages" role="tabpanel" class="tab-pane" id="lang-{{$index}}">

        <a class="btn btn-xs btn-danger" ng-click="cmsg.deleteLanguage(lang)"><i class="material-icons mr-0">&#xE15B;</i></a>
        <br>
        <div class="form-group">
            <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Language');?>*</label>
            <div class="row">
                <div class="col-xs-3" ng-repeat="langDialtect in cmsg.dialects">
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

        <ul class="nav nav-pills" role="tablist">
            <li role="presentation" class="active"><a href="#main-extension-lang-{{$index}}" aria-controls="main-extension-lang-{{$index}}" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Messages');?></a></li>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_lang_tab_multiinclude.tpl.php')); ?>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="main-extension-lang-{{$index}}">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?>*</label>
                    <textarea class="form-control" rows="5" name="message_lang[{{$index}}]" ng-model="lang.message"></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Fallback message');?></label>
                    <textarea class="form-control" rows="5" name="fallback_message_lang[{{$index}}]" ng-model="lang.fallback_message"></textarea>
                </div>
            </div>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_lang_tab_content_multiinclude.tpl.php')); ?>
        </div>

    </div>

</div>

</div>
<script>
    $('.live-help-tooltip').tooltip();
</script>