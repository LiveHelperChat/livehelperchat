<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<div id="transfer-block-<?php echo $chat->id?>"></div>

<div role="tabpanel">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#transferusermodal" aria-controls="transferusermodal" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer to a user');?></a></li>
		<li role="presentation"><a href="#transferdepmodal" aria-controls="transferdepmodal" role="tab" data-toggle="tab"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer to a department');?></a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="transferusermodal">
		
    		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Logged in users');?></h4>
    
      		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer a chat to one of your departments users');?></p>
    
      		<?php foreach (erLhcoreClassChat::getOnlineUsers(array($user_id)) as $key => $user) : ?>
    		<label><input type="radio" name="TransferTo<?php echo $chat->id?>" value="<?php echo $user['id']?>" <?php echo $key == 0 ? 'checked="checked"' : ''?>> <?php echo htmlspecialchars($user['name'])?> <?php echo htmlspecialchars($user['surname'])?></label><br/>
    		<?php endforeach; ?>
    
    		<input type="button" onclick="lhinst.transferChat('<?php echo $chat->id;?>')" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer');?>" />
    		
		</div>
		<div role="tabpanel" class="tab-pane" id="transferdepmodal">
    		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Departments');?></h4>
    
      		<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) :
      		if ($departament['id'] !== $chat->dep_id) : ?>
    	    <label><input type="radio" name="DepartamentID<?php echo $chat->id?>" value="<?php echo $departament['id']?>"/> <?php echo htmlspecialchars($departament['name'])?></label><br/>
    	    <?php endif; endforeach; ?>
    
    		<input type="button" onclick="lhinst.transferChatDep('<?php echo $chat->id;?>')" class="btn btn-default" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer');?>" />
		</div>
	</div>
</div>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>