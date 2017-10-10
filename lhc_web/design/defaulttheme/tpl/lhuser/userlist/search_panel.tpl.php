<form action="<?php echo $input->form_action?>" method="get" name="SearchFormRight">

	<input type="hidden" name="doSearch" value="1">

	<div class="row">
		<div class="col-md-3">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Username');?></label>
			<input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($input->username)?>" />
		   </div>
		</div>
        <div class="col-md-3">
		   <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Nickname');?></label>
			<input type="text" class="form-control" name="chat_nickname" value="<?php echo htmlspecialchars($input->chat_nickname)?>" />
		   </div>
		</div>
		<div class="col-md-3">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','E-mail');?></label>
			<input type="text" class="form-control" name="email" value="<?php echo htmlspecialchars($input->email)?>" />
		  </div>
		</div>
		<div class="col-md-3">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Name');?></label>
			<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($input->name)?>" />
		  </div>
		</div>
		<div class="col-md-3">
		  <div class="form-group">
			<label><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Surname');?></label>
			<input type="text" class="form-control" name="surname" value="<?php echo htmlspecialchars($input->surname)?>" />
		  </div>
		</div>				
	</div>

	<div class="btn-group" role="group" aria-label="...">
		<input type="submit" name="doSearch" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/lists/search_panel','Search');?>" />	
	</div>

</form>
