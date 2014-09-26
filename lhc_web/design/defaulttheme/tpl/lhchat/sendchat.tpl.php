<div class="mb0" style="width:250px;padding:0px 0 10px 0;">
	<form id="user-action">
			<input type="text" placeholder="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendchat','Enter your e-mail')?>" title="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/sendchat','Enter your e-mail')?>" name="UserEmail" value="<?php echo htmlspecialchars($chat->email)?>" />
			<ul class="button-group round">
					<li><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Send')?>" class="button tiny success mb0" onclick="lhinst.sendemail()"></li>
					<li><input type="button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('system/buttons','Cancel')?>" class="button tiny alert mb0" onclick="lhinst.cancelcolorbox()"></li>
			</ul>
	</form>
</div>