 <?php 
$modalHeaderClass = 'pt-1 pb-1 pl-2 pr-2';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatpreview','Chat preview');
$modalSize = 'md';
$modalBodyClass = 'p-1'
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>
<div id="messages" class="mt-4">
    <div id="messagesBlock"><?php
    $lastMessageID = 0;
    $lastOperatorChanged = false;
    $lastOperatorId = false;
    
    foreach (erLhcoreClassChat::getChatMessages($chat->id) as $msg) : 
    
    if ($lastOperatorId !== false && $lastOperatorId != $msg['user_id']) {
        $lastOperatorChanged = true;
    } else {
        $lastOperatorChanged = false;
    }
    
    $lastOperatorId = $msg['user_id'];        
    ?>        		
    <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/user_msg_row.tpl.php'));?>	        	
    <?php $lastMessageID = $msg['id']; 
     endforeach; ?>
   </div>
</div>

<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_footer.tpl.php'));?>