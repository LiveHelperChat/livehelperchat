<?php $modalHeaderTitle = 'Choose a survey'?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<div id="survey-block-<?php echo $chat->id?>"></div>

<div class="mx170">
    <?php foreach (erLhAbstractModelSurvey::getList() as $item) : ?>
        <div><label><input type="radio" name="SurveyItem<?php echo $chat->id?>" value="<?php echo $item->id?>" />&nbsp;<?php echo htmlspecialchars($item->name)?></label></div>
    <?php endforeach;?>
</div>

<br/>
<input type="submit" value="Request user" class="btn btn-default" onclick="lhinst.chooseSurvey('<?php echo $chat->id;?>')" />

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>