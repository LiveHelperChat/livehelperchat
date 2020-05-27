<div ng-repeat="lang in cmsg.languages" role="tabpanel" class="tab-pane" id="lang-{{$index}}">

    <?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/language_choose.tpl.php'));?>

    <?php if (!isset($autoResponderOptions['hide_pending']) || $autoResponderOptions['hide_pending'] === false) : ?>
    <div class="form-group">
        <label><?php echo $fields['wait_message']['trans'];?></label>
        <?php $bbcodeOptions = array('selector' => '#trans_wait_message_{{$index}}'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
        <textarea class="form-control" id="trans_wait_message_{{$index}}" ng-model="lang.wait_message" name="wait_message[{{$index}}]"></textarea>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <label><?php echo $fields['multilanguage_message']['trans'];?></label>
        <?php $bbcodeOptions = array('selector' => '#trans_multilanguage_message_{{$index}}'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
        <textarea class="form-control" id="trans_multilanguage_message_{{$index}}" ng-model="lang.multilanguage_message" name="multilanguage_message[{{$index}}]"></textarea>
    </div>

    <?php if (!isset($autoResponderOptions['hide_operator_nick']) || $autoResponderOptions['hide_operator_nick'] === false) : ?>
    <div class="form-group">
        <label><?php echo $fields['operator']['trans'];?></label>
        <input type="text" class="form-control" ng-model="lang.operator" value="" name="operator[{{$index}}]">
    </div>
    <?php endif; ?>

    <?php if (!isset($autoResponderOptions['hide_wait_message']) || $autoResponderOptions['hide_wait_message'] === false) : ?>
    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Pending chat messaging');?></h4>
    
    <div class="row">
        <div class="col-6">
            <div class="form-group">
                <label><?php echo $fields['timeout_message']['trans'];?> [1]</label>
                <?php $bbcodeOptions = array('selector' => '#trans_timeout_message_{{$index}}'); ?>
                <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                <textarea class="form-control" id="trans_timeout_message_{{$index}}" ng-model="lang.timeout_message" name="timeout_message[{{$index}}]"></textarea>
            </div>
        </div>
        <?php for ($i = 2; $i <= 5; $i++) : ?>
        <div class="col-6">
            <div class="form-group">
                <label><?php echo $fields['timeout_message_' . $i]['trans'];?> [<?php echo $i?>]</label>
                <?php $bbcodeOptions = array('selector' => '#trans_timeout_message_{{$index}}_'.$i); ?>
                <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                <textarea class="form-control" id="trans_timeout_message_{{$index}}_<?php echo $i?>" ng-model="lang.timeout_message_<?php echo $i?>" name="timeout_message_<?php echo $i?>[{{$index}}]"></textarea>
            </div>
        </div>
        <?php endfor;?>
    </div>
    <?php endif; ?>

    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Visitor not replying messaging');?></h4>
    <div class="row">
    <?php for ($i = 1; $i <= 5; $i++) : ?>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo $fields['timeout_reply_message_' . $i]['trans'];?></label>
                    <?php $bbcodeOptions = array('selector' => '#trans_timeout_reply_message_{{$index}}_'.$i); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                    <textarea class="form-control" id="trans_timeout_reply_message_{{$index}}_<?php echo $i?>" ng-model="lang.timeout_reply_message_<?php echo $i?>" name="timeout_reply_message_<?php echo $i?>[{{$index}}]"></textarea>
                 </div>
            </div>
    <?php endfor;?>
    </div>

    <?php /**  **/ ?>
    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','Operator not replying messaging');?></h4>
    <div class="row">
    <?php for ($i = 1; $i <= 5; $i++) : ?>
            <div class="col-6">
                <div class="form-group">
                    <label><?php echo $fields['timeout_reply_message_' . $i]['trans'];?></label>
                    <?php $bbcodeOptions = array('selector' => '#trans_timeout_op_trans_reply_message_{{$index}}_'.$i); ?>
                    <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                    <textarea class="form-control" id="trans_timeout_op_trans_reply_message_{{$index}}_<?php echo $i?>" ng-model="lang.timeout_op_trans_reply_message_<?php echo $i?>" name="timeout_op_trans_reply_message_<?php echo $i?>[{{$index}}]"></textarea>
                 </div>
            </div>
    <?php endfor;?>
    </div>

    <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/widgettheme','On-hold chat messaging');?></h4>

    <div class="form-group">
        <label><?php echo $fields['wait_timeout_hold']['trans'];?></label>
        <?php $bbcodeOptions = array('selector' => '#trans_wait_timeout_hold_{{$index}}'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
        <textarea class="form-control" id="trans_wait_timeout_hold_{{$index}}" ng-model="lang.wait_timeout_hold" value="" name="wait_timeout_hold[{{$index}}]"></textarea>
    </div>

    <div class="row">
    <?php for ($i = 1; $i <= 5; $i++) : ?>
        <div class="col-6">
            <div class="form-group">
                <label><?php echo $fields['timeout_hold_message_' . $i]['trans'];?></label>
                <?php $bbcodeOptions = array('selector' => '#trans_timeout_hold_message_{{$index}}_'.$i); ?>
                <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
                <textarea class="form-control" id="trans_timeout_hold_message_{{$index}}_<?php echo $i?>" ng-model="lang.timeout_hold_message_<?php echo $i?>" value="" name="timeout_hold_message_<?php echo $i?>[{{$index}}]"></textarea>
              </div>
        </div>
    <?php endfor;?>
    </div>

    <?php if (!isset($autoResponderOptions['hide_personal_closing']) || $autoResponderOptions['hide_personal_closing'] === false) : ?>
    <h4><?php echo $fields['close_message']['trans'];?></h4>
    <div class="form-group">
        <?php $bbcodeOptions = array('selector' => '#trans_timeout_message_{{$index}}'); ?>
        <?php include(erLhcoreClassDesign::designtpl('lhbbcode/toolbar.tpl.php')); ?>
        <textarea class="form-control" id="trans_close_message_{{$index}}" ng-model="lang.close_message" name="close_message[{{$index}}]"></textarea>
    </div>
    <?php endif; ?>



</div>