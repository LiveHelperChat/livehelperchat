<script type="text/javascript" src="<?php echo erLhcoreClassDesign::designJS('js/survey.js');?>"></script>

<?php $fields = $object->getFields();?>

<?php if (isset($errors)) : ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/validation_error.tpl.php'));?>
<?php endif; ?>

<?php if (isset($updated) && $updated == true) : $msg = erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/abstract_form','Updated!'); ?>
	<?php include(erLhcoreClassDesign::designtpl('lhkernel/alert_success.tpl.php'));?>
<?php endif; ?>

<div class="form-group">
<label><?php echo $fields['name']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('name', $fields['name'], $object)?>
</div>

<div class="form-group">
<label><?php echo $fields['feedback_text']['trans'];?></label>
<?php echo erLhcoreClassAbstract::renderInput('feedback_text', $fields['feedback_text'], $object)?>
</div>

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
    		<?php if ($object->{$keyOption . '_pos'} == $i) : ?>
    		<div class="row" id="position-id-<?php echo $counterPosition;?>">    			
	    		<div class="col-xs-12">
	    			<label><?php echo erLhcoreClassAbstract::renderInput($sortOption['field'] . '_enabled', $fields[$sortOption['field'] . '_enabled'], $object)?> <?php echo $titleOptions[$sortOption['type']]?></label>

	        	    <div class="btn-group pull-right" role="group" aria-label="...">
						<button type="button" class="btn btn-default btn-xs" onclick="adminSurvey.moveUp('<?php echo $sortOption['field']?>')"><i class="material-icons">trending_up</i></button>
						<button type="button" class="btn btn-default btn-xs" onclick="adminSurvey.moveDown('<?php echo $sortOption['field']?>')"><i class="material-icons">trending_down</i></button>
				    </div>

	        		<?php if ($sortOption['type'] == 'stars') : ?>
		        		<div class="row" ng-show="abstract_checked_<?php echo $sortOption['field'] . '_enabled'?>">
		        		  <div class="col-xs-12">
		        		        <label><?php echo erLhcoreClassAbstract::renderInput($keyOption . '_req', $fields[$keyOption. '_req'], $object)?> <?php echo $fields[$keyOption . '_req']['trans'];?></label>
		        		  </div>
		        		  <div class="col-xs-6">
		        		      <div class="form-group">
		        				<label><?php echo $fields[$keyOption . '_title']['trans'];?></label>
		        				<?php echo erLhcoreClassAbstract::renderInput($keyOption . '_title', $fields[$keyOption. '_title'], $object)?>
		        			  </div>
		        		  </div>
		        		  <div class="col-xs-6">
		            		  <div class="form-group">
		            			<label><?php echo $fields[$sortOption['field']]['trans'];?></label>
		            			<?php echo erLhcoreClassAbstract::renderInput($sortOption['field'], $fields[$sortOption['field']], $object)?>
		            		  </div>
		        		  </div>
		        		</div>
	        		<?php elseif ($sortOption['type'] == 'question') : ?>
	        			<div ng-show="abstract_checked_<?php echo $sortOption['field'] . '_enabled'?>">
	        				<label><?php echo erLhcoreClassAbstract::renderInput($keyOption . '_req', $fields[$keyOption. '_req'], $object)?> <?php echo $fields[$keyOption . '_req']['trans'];?></label>
	        			</div>
	        			<div class="form-group" ng-show="abstract_checked_<?php echo $sortOption['field'] . '_enabled'?>">
	        				<label><?php echo $fields[$sortOption['field']]['trans'];?></label>
	        				<?php echo erLhcoreClassAbstract::renderInput($sortOption['field'], $fields[$sortOption['field']], $object)?>
	        			</div>
	        		<?php elseif ($sortOption['type'] == 'question_options') : ?>	        			
	        		    <div class="question-rows-container" ng-show="abstract_checked_<?php echo $sortOption['field'] . '_enabled'?>">	
    	        		    <div ng-show="abstract_checked_<?php echo $sortOption['field'] . '_enabled'?>">
    	        				<label><?php echo erLhcoreClassAbstract::renderInput($keyOption . '_req', $fields[$keyOption. '_req'], $object)?> <?php echo $fields[$keyOption . '_req']['trans'];?></label>
    	        			</div>
	                    	<div class="form-group">
	                			<label><?php echo $fields[$sortOption['field']]['trans'];?></label>
	                    		<div class="row">
	    		                    <div class="col-xs-8">
	            				        <?php echo erLhcoreClassAbstract::renderInput($sortOption['field'], $fields[$sortOption['field']], $object)?>
	            					</div>
	                    			<div class="col-xs-4">
	                    			    <input type="button" class="btn btn-default btn-block" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('survey/form','Add option')?>" onclick="adminSurvey.addOptionAnswer('<?php echo $sortOption['field']?>')"/>
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
	<input type="submit" class="btn btn-default" name="SaveClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Save');?>"/>
	<input type="submit" class="btn btn-default" name="UpdateClient" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Update');?>"/>
	<input type="submit" class="btn btn-default" name="CancelAction" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel');?>"/>
</div>