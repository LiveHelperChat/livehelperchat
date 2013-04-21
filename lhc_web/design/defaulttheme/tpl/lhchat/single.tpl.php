<script type="text/javascript">
	$(function() {
            <?php if (is_numeric($chat_id)) : ?>
            addChat(<?php echo $chat_id;?>,'<?php echo $chat_to_load->nick;?>');
            <?php endif; ?>
	});
</script>

<?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>

<div class="section-container tabs" data-section="tabs" id="tabs">

</div>

<script type="text/javascript">
function addChat(chat_id,name)
{
    lhinst.startChat(chat_id,$('#tabs'),name);
    lhinst.setCloseWindowOnEvent(true);
    window.focus();
}
</script>