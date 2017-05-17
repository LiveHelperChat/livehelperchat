<div class="fs12">
<h2><?php echo htmlspecialchars($chat->nick)?><?php $chat->city != '' ? print ', '.htmlspecialchars($chat->city) : ''?>, <?php echo date(erLhcoreClassModule::$dateDateHourFormat,$chat->time)?> <div class="right">IP:<?php echo htmlspecialchars($chat->ip)?>, ID: <?php echo $chat->id?></div></h2>

<?php $collectedSurveys = erLhAbstractModelSurveyItem::getList(array('filter' => array('chat_id' => $chat->id)));?>

<?php foreach ($collectedSurveys as $survey_item) : $survey = $survey_item->survey; ?>
<hr>
    <h4><?php echo htmlspecialchars($survey->name)?></h4>
    <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));?>
    <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/collecteditem_display.tpl.php'));?>
<?php endforeach; ?>

<?php if ($chat->remarks != '') : ?>
<hr>
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Remarks')?></h4>
<p><?php echo erLhcoreClassBBCode::make_clickable(htmlspecialchars($chat->remarks))?></p>
<?php endif;?>
<hr>
<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/adminchat','Messages')?></h4>
<br>
<?php include(erLhcoreClassDesign::designtpl('lhchat/lists/msg_obj_list_admin.tpl.php'));?>
</div>