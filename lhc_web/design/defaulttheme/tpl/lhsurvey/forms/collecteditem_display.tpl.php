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
					echo erLhcoreClassSurveyValidator::parseAnswer($options[$survey_item->{$sortOption['field']}-1]['option']);
				} else {
					echo erLhcoreClassSurveyValidator::parseAnswer($survey_item->{$sortOption['field']});
				}
				?>
				</p>
			</div>
			<?php endif;?> 	    		
		<?php endif;?>
	<?php endforeach;?>
<?php endfor;?> 