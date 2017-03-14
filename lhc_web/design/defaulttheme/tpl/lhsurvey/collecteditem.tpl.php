<?php $modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('survey/collected','Collected information')?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<div class="row">    
    <div class="columns col-md-12"> 
        <?php include(erLhcoreClassDesign::designtpl('lhsurvey/forms/fields_names.tpl.php'));?>
        <?php for ($i = 0; $i < 16; $i++) : ?>    
	    	<?php foreach ($sortOptions as $keyOption => $sortOption) : ?>    	   		    
	    		<?php if ($survey->{$keyOption . '_pos'} == $i && $survey->{$keyOption . '_enabled'} == 1) : ?>	    		
	    		<?php if ($sortOption['type'] == 'stars') : ?>    	
    				<div class="form-group">
				    	<label><?php echo htmlspecialchars($survey->{$sortOption['field'] . '_title'});?></label>
				    	<p><?php echo htmlspecialchars($survey_item->{$sortOption['field']})?></p>
				    </div>
    				<?php elseif ($sortOption['type'] == 'question') : ?>
    				<div class="form-group">
    					<label><?php echo htmlspecialchars($survey->{$sortOption['field']});?></label>
    					<p><?php echo htmlspecialchars($survey_item->{$sortOption['field']})?></p>
    				</div>
    				<?php elseif ($sortOption['type'] == 'question_options') : ?>    				
    				<div class="form-group">
    					<label><?php echo $survey->{$sortOption['field']};?></label>
    					<p>
    					<?php 
    					$options = $survey->{$sortOption['field'] . '_items_front'};  					
    					if (isset($options[$survey_item->{$sortOption['field']}-1])) {
    						echo htmlspecialchars($options[$survey_item->{$sortOption['field']}-1]['option']);
    					} else {
    						echo htmlspecialchars($survey_item->{$sortOption['field']});
    					}
						?>
						</p>
    				</div>
    				<?php endif;?> 	    		
	    		<?php endif;?>
	    	<?php endforeach;?>
    	<?php endfor;?>        
    </div>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>