 <?php 
$modalHeaderClass = 'small-modal-header';
$modalHeaderTitle = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/chatpreview','Chat preview');
$modalSize = 'md';
$modalBodyClass = 'widget-modal-body'
?>
<?php include(erLhcoreClassDesign::designtpl('lhkernel/modal_header.tpl.php'));?>

<div id="messages" class="pt20">
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