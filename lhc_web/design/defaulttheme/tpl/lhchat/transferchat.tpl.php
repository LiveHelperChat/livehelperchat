<br>
<div id="transfer-block-<?php echo $chat->id?>"></div>

<div class="section-container auto" data-section>
  <section class="active">
    <p class="title" data-section-title><a href="#panel1"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer to a user');?></a></p>
    <div class="content" data-section-content>
    	<div>
        <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Logged in users');?></h4>

  		<p><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer a chat to one of your departments users');?></p>

  		<?php foreach (erLhcoreClassChat::getOnlineUsers(array($user_id)) as $key => $user) : ?>
		    <label><input type="radio" name="TransferTo<?php echo $chat->id?>" value="<?php echo $user['id']?>" <?php echo $key == 0 ? 'checked="checked"' : ''?>> <?php echo htmlspecialchars($user['name'])?> <?php echo htmlspecialchars($user['surname'])?></label>
		<?php endforeach; ?>

		<input type="button" onclick="lhinst.transferChat('<?php echo $chat->id;?>')" class="small button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer');?>" />
    </div>
    </div>
  </section>
  <section>
    <p class="title" data-section-title><a href="#panel2"><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer to a department');?></a></p>
    <div class="content" data-section-content>
    	<div>
      <h4><?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Departments');?></h4>

  		<?php foreach (erLhcoreClassDepartament::getDepartaments() as $departament) :
  		if ($departament['id'] !== $chat->dep_id) : ?>
	        <label><input type="radio" name="DepartamentID<?php echo $chat->id?>" value="<?php echo $departament['id']?>"<?php in_array($departament['id'],$userDepartaments) ? print 'checked="checked"' : '';?>/> <?php echo htmlspecialchars($departament['name'])?></label>
	    <?php endif; endforeach; ?>

		<input type="button" onclick="lhinst.transferChatDep('<?php echo $chat->id;?>')" class="small button" value="<?php echo erTranslationClassLhTranslation::getInstance()->getTranslation('chat/transferchat','Transfer');?>" />
    </div>
    </div>
  </section>
</div>

<script>setTimeout(function(){$(document).foundation('section', 'resize');},1000)</script>