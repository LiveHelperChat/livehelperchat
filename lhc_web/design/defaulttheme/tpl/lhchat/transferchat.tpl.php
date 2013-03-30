<br>
<dl class="tabs">
    <dd class="active"><a href="#simpleTransfer1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer to user');?></a></dd>
    <dd><a href="#simpleTransfer2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer to department');?></a></dd>
</dl>

<div id="transfer-block-<?php echo $chat->id?>"></div>

<ul class="tabs-content" id="tabs-content">
  <li id="simpleTransfer1Tab" class="active">
  		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Logged users');?></h4>

  		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer chat to one of your departments users');?></p>

  		<?php foreach (erLhcoreClassChat::getOnlineUsers(array($user_id)) as $key => $user) : ?>
		    <label><input type="radio" name="TransferTo<?php echo $chat->id?>" value="<?php echo $user['id']?>" <?php echo $key == 0 ? 'checked="checked"' : ''?>> <?php echo htmlspecialchars($user['name'])?> <?php echo htmlspecialchars($user['surname'])?></label>
		<?php endforeach; ?>

		<input type="button" onclick="lhinst.transferChat('<?php echo $chat->id;?>')" class="small button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer');?>" />
  </li>
  <li id="simpleTransfer2Tab" >
  		<h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Departments');?></h4>

  		<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) :
  		if ($departament['id'] !== $chat->dep_id) : ?>
	        <label><input type="radio" name="DepartamentID<?php echo $chat->id?>" value="<?php echo $departament['id']?>"<?php in_array($departament['id'],$userDepartaments) ? print 'checked="checked"' : '';?>/> <?php echo htmlspecialchars($departament['name'])?></label>
	    <?php endif; endforeach; ?>

		<input type="button" onclick="lhinst.transferChatDep('<?php echo $chat->id;?>')" class="small button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer');?>" />
  </li>
</ul>