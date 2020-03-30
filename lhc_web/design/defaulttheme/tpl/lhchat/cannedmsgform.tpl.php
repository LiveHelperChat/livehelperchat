<div ng-controller="CannedMsgCtrl as cmsg" ng-cloak ng-init='<?php if ($canned_message->languages != '') : ?>cmsg.languages = <?php echo $canned_message->languages?>;<?php endif;?>cmsg.dialects = <?php echo json_encode(array_values(erLhcoreClassModelSpeechLanguageDialect::getDialectsGrouped()))?>'>

<ul class="nav nav-pills" role="tablist" id="canned-main-tabs">
    <li role="presentation" class="nav-item" ><a class="nav-link <?php if ( (isset($tab) && $tab == 'main') || !isset($tab)) : ?>active<?php endif;?>" href="#main" aria-controls="main" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Main');?></a></li>
    <li ng-repeat="lang in cmsg.languages" class="nav-item" role="presentation"><a href="#lang-{{$index}}" class="nav-link" aria-controls="lang-{{$index}}" role="tab" data-toggle="tab" ><i class="material-icons mr-0">&#xE894;</i> [{{cmsg.getLanguagesChecked(lang)}}]</a></li>
    <li class="nav-item"><a href="#addlanguage" class="nav-link" ng-click="cmsg.addLanguage()"><i class="material-icons">&#xE145;</i><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Add translation');?></a></li>
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

        <?php $showAnyDepartment = erLhcoreClassUser::instance()->hasAccessTo('lhchat','see_global'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/department.tpl.php')); ?>

        <?php include(erLhcoreClassDesign::designtpl('lhchat/part/after_cannedmsgform_multiinclude.tpl.php')); ?>

        <ul class="nav nav-pills" role="tablist" id="canned-main-extension">
            <li role="presentation" class="nav-item"><a class="active nav-link" href="#main-extension" aria-controls="main-extension" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Messages');?></a></li>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_tab_multiinclude.tpl.php')); ?>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="main-extension">
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Message');?>*</label>
                    <?php $bbcodeOptions = array('selector' => '#canned-message'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                    <textarea class="form-control" rows="5" id="canned-message" name="Message"><?php echo htmlspecialchars($canned_message->msg);?></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Fallback message');?></label>
                    <?php $bbcodeOptions = array('selector' => '#id-FallbackMessage'); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                    <textarea class="form-control" id="id-FallbackMessage" rows="5" name="FallbackMessage"><?php echo htmlspecialchars($canned_message->fallback_msg);?></textarea>
                </div>
                <div class="form-group">
                    <label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','HTML Snippet');?></label>
                    <textarea class="form-control" rows="5" name="HTMLSnippet"><?php echo htmlspecialchars($canned_message->html_snippet);?></textarea>
                </div>
            </div>
            <?php include(erLhcoreClassDesign::designtpl('lhchat/cannedmsg/custom_fallback_tab_content_multiinclude.tpl.php')); ?>
        </div>

    </div>

    <div ng-repeat="lang in cmsg.languages" role="tabpanel" class="tab-pane" id="lang-{{$index}}">

        <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/language_choose.tpl.php'));?>

        <ul class="nav nav-pills" role="tablist">
            <li role="presentation" class="nav-item"><a class="active nav-link" href="#main-extension-lang-{{$index}}" aria-controls="main-extension-lang-{{$index}}" role="tab" data-toggle="tab" ><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/cannedmsg','Messages');?></a></li>
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

</div>
<script>
    $('.live-help-tooltip').tooltip();
</script>