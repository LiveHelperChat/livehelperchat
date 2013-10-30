<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight">

	<input type="hidden" name="doSearch" value="1">

	<div class="row">
		<div class="columns large-3">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Nick');?></label>
		</div>
		<div class="columns large-3">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','E-mail');?></label>
		</div>
		<div class="columns large-4">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Date range from to');?></label>
		</div>
		<div class="columns large-2">
		</div>
	</div>

	<div class="row">
		<div class="columns large-3">
			<input type="text" name="nick" value="<?php echo htmlspecialchars($input->nick)?>" />
		</div>
		<div class="columns large-3">
			<input type="text" name="email" value="<?php echo htmlspecialchars($input->email)?>" />
		</div>
		<div class="columns large-4">

			<div class="row">
				<div class="columns large-6">
					<input type="text" name="timefrom" placeholder="E.g <?php echo date('Y-m-d',time()-7*24*3600)?>" value="<?php echo htmlspecialchars($input->timefrom)?>" />
				</div>
				<div class="columns large-6">
					<input type="text" name="timeto" placeholder="E.g <?php echo date('Y-m-d')?>" value="<?php echo htmlspecialchars($input->timeto)?>" />
				</div>
			</div>

		</div>
		<div class="columns large-2">
			<input type="submit" name="doSearch" class="button radius small expand" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />
		</div>
	</div>

</form>