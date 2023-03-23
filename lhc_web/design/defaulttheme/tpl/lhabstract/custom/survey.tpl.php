<script type="text/javascript" src="<?php echo erLhcoreClassDesign::designJS('js/survey.js');?>"></script>

<?php $fields = $object->getFields();?>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/abstract_form','Updated!'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $fields['name']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object)?>
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label><?php echo $fields['identifier']['trans'];?></label>
            <?php echo erLhcoreClassAbstract::renderInput('identifier', $fields['identifier'], $object)?>
        </div>
    </div>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/survey/custom_multiinclude.tpl.php'));?>

<?php $translatableItem = array('identifier' => 'feedback_text'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

<?php $translatableItem = array('identifier' => 'survey_title'); ?>
<?php include(erLhcoreClassDesign::designtpl('lhabstract/custom/theme/theme_text_translatable.tpl.php'));?>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('disable_chat_preview', $fields['disable_chat_preview'], $object)?> <?php echo $fields['disable_chat_preview']['trans'];?></label>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('disable_chat_download', $fields['disable_chat_download'], $object)?> <?php echo $fields['disable_chat_download']['trans'];?></label>
</div>

<div class="form-group">
    <label><?php echo erLhcoreClassAbstract::renderInput('return_on_close', $fields['return_on_close'], $object)?> <?php echo $fields['return_on_close']['trans'];?></label>
</div>

<hr>


<?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));?>

<?php 

$counterPosition = 0;
$titleOptions = array (
	'stars' => erTranslationClassLhTranslation::getInstance()->getTranslation('survey/form','Enable stars'),
	'question' => erTranslationClassLhTranslation::getInstance()->getTranslation('survey/form','Enable question'),
	'question_options' => erTranslationClassLhTranslation::getInstance()->getTranslation('survey/form','Enable question with an answers')
);

?>

<div class="form-elements">    
    <?php for ($i = 0; $i < 16; $i++) : ?>    
    	<?php foreach ($sortOptions as $keyOption => $sortOption) : ?>
    		<?php  if ($object->{$keyOption . '_pos'} == $i) : ?>
    		<div class="row" id="position-id-<?php echo $counterPosition;?>">    			
	    		<div class="col-12 border-top pt-2">
	    			<label class="fw-bold"><?php echo erLhcoreClassAbstract::renderInput($sortOption['field'] . '_enabled', $fields[$sortOption['field'] . '_enabled'], $object)?> <?php preg_match_all('/\d+/is',$sortOption['field'], $matches); print_r($matches[0][0])?>. <?php echo $titleOptions[$sortOption['type']]?></label>

	        	    <div class="btn-group float-end" role="group" aria-label="...">
						<button type="button" class="btn btn-secondary btn-xs" onclick="adminSurvey.moveUp('<?php echo $sortOption['field']?>')"><i class="material-icons">trending_up</i></button>
						<button type="button" class="btn btn-secondary btn-xs" onclick="adminSurvey.moveDown('<?php echo $sortOption['field']?>')"><i class="material-icons">trending_down</i></button>
				    </div>

	        		<?php if ($sortOption['type'] == 'stars') : ?>
		        		<div class="row" ng-show="abstract_checked_<?php echo $sortOption['field'] . '_enabled'?>">
		        		  <div class="col-12">
		        		        <label><?php echo erLhcoreClassAbstract::renderInput($keyOption . '_req', $fields[$keyOption. '_req'], $object)?> <?php echo $fields[$keyOption . '_req']['trans'];?></label>
		        		  </div>
		        		  <div class="col-6">
		        		      <div class="form-group">
		        				<label><?php echo $fields[$keyOption . '_title']['trans'];?></label>
		        				<?php echo erLhcoreClassAbstract::renderInput($keyOption . '_title', $fields[$keyOption. '_title'], $object)?>
		        			  </div>
		        		  </div>
		        		  <div class="col-6">
		            		  <div class="form-group">
		            			<label><?php echo $fields[$sortOption['field']]['trans'];?></label>
		            			<?php echo erLhcoreClassAbstract::renderInput($sortOption['field'], $fields[$sortOption['field']], $object)?>
		            		  </div>
		        		  </div>
		        		</div>
	        		<?php elseif ($sortOption['type'] == 'question') : ?>
                        <div class="row" ng-show="abstract_checked_<?php echo $sortOption['field'] . '_enabled'?>">
                            <div class="col-12">
                                <label><?php echo erLhcoreClassAbstract::renderInput($keyOption . '_req', $fields[$keyOption. '_req'], $object)?> <?php echo $fields[$keyOption . '_req']['trans'];?></label>
                            </div>
                            <div class="col-6 form-group" ng-show="abstract_checked_<?php echo $sortOption['field'] . '_enabled'?>">
                                <label><?php echo $fields[$sortOption['field']]['trans'];?></label>
                                <?php echo erLhcoreClassAbstract::renderInput($sortOption['field'], $fields[$sortOption['field']], $object)?>
                            </div>
                            <div class="col-3">
                                <label><?php echo $fields['min_stars_' . $sortOption['field']]['trans'];?></label>
                                <input type="number" name="AbstractInput_min_stars_<?php echo $sortOption['field']?>" class="form-control form-control-sm" value="<?php echo isset($object->configuration_array['min_stars_' . $sortOption['field']]) ? htmlspecialchars($object->configuration_array['min_stars_' . $sortOption['field']]) : ''?>" >
                            </div>
                            <div class="col-3">
                                <label><?php echo $fields['star_field_' . $sortOption['field']]['trans'];?></label>
                                <input type="number" name="AbstractInput_star_field_<?php echo $sortOption['field']?>" max="5" class="form-control form-control-sm" value="<?php echo isset($object->configuration_array['star_field_' . $sortOption['field']]) ? htmlspecialchars($object->configuration_array['star_field_' . $sortOption['field']]) : ''?>" >
                            </div>
                        </div>
	        		<?php elseif ($sortOption['type'] == 'question_options') : ?>
	        		    <div class="question-rows-container" ng-show="abstract_checked_<?php echo $sortOption['field'] . '_enabled'?>">
    	        		    <div ng-show="abstract_checked_<?php echo $sortOption['field'] . '_enabled'?>">
    	        				<label><?php echo erLhcoreClassAbstract::renderInput($keyOption . '_req', $fields[$keyOption. '_req'], $object)?> <?php echo $fields[$keyOption . '_req']['trans'];?></label>
    	        			</div>
	                    	<div class="form-group">
	                			<label><?php echo $fields[$sortOption['field']]['trans'];?></label>
	                    		<div class="row">
	    		                    <div class="col-8">
	            				        <?php echo erLhcoreClassAbstract::renderInput($sortOption['field'], $fields[$sortOption['field']], $object)?>
	            					</div>
	                    			<div class="col-4">
	                    			    <input type="button" class="btn btn-secondary btn-block" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/form','Add option')?>" onclick="adminSurvey.addOptionAnswer('<?php echo $sortOption['field']?>')"/>
	                    			</div>
	                			</div>
	            			</div>
	            			<div class="form-group">
	            				<textarea class="form-control" name="AbstractInput_<?php echo $sortOption['field']?>_items" id="id_<?php echo $sortOption['field']?>_items" rows="5" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/form','Enter a possible answer to your question here...')?>"><?php echo htmlspecialchars($object->{$sortOption['field'] . '_items'})?></textarea>
	                		</div>
	        		   </div>
	        		<?php endif;?>
	        	</div>	        	
	        	<input type="hidden" class="pos-attribute" data-field="<?php echo $sortOption['field']?>" id="id_<?php echo $sortOption['field'] . '_pos'?>" name="AbstractInput_<?php echo $sortOption['field'] . '_pos'?>" value="<?php echo $counterPosition;?>" />
        	</div>
    		<?php $counterPosition++; endif; ?>
    	<?php endforeach;?>    
    <?php endfor;?>
</div>

<div class="btn-group" role="group" aria-label="...">
	<input type="submit" class="btn btn-secondary" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
	<input type="submit" class="btn btn-secondary" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
	<input type="submit" class="btn btn-secondary" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
</div>