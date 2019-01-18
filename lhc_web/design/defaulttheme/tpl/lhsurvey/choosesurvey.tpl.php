<?php $modalHeaderTitle = 'Choose a survey'?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<ul class="nav nav-pills" role="tablist">
	<li role="presentation" class="active"><a href="#survey-<?php echo $chat->id?>" aria-controls="survey-<?php echo $chat->id?>" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/choosesurvey','Survey');?></a></li>
	<li role="presentation" ><a href="#collected-<?php echo $chat->id?>" aria-controls="collected-<?php echo $chat->id?>" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/choosesurvey','Collected information');?></a></li>
</ul>

<div class="tab-content">
	<div role="tabpanel" class="tab-pane active" id="survey-<?php echo $chat->id?>">
    	<div id="survey-block-<?php echo $chat->id?>"></div>
    
        <div class="mx170">
            <?php foreach (erLhAbstractModelSurvey::getList() as $item) : ?>
                <div><label><input type="radio" name="SurveyItem<?php echo $chat->id?>" value="<?php echo $item->id?>" />&nbsp;<?php echo htmlspecialchars($item->name)?></label></div>
            <?php endforeach;?>
        </div>
        
        <br/>
        <input type="submit" value="Request user" class="btn btn-default" onclick="lhinst.chooseSurvey('<?php echo $chat->id;?>')" />
	</div>
	<div role="tabpanel" class="tab-pane" id="collected-<?php echo $chat->id?>">
	   <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));?>
	   <?php foreach (erLhAbstractModelSurveyItem::getList(array('filter' => array('chat_id' => $chat->id))) as $survey_item) : $survey = $survey_item->survey;?>
    	   <h3><?php echo htmlspecialchars($survey_item->survey)?></h3>	   
    	   <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/collecteditem_display.tpl.php'));?>
	   <?php endforeach; ?>
	</div>
</div>




<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>