<script type="text/javascript">
	$(function() {
            <?php if (is_numeric($chat_id)) : ?>
            addChat(<?php echo $chat_id;?>,'<?php echo erLhcoreClassDesign::shrt($chat_to_load->nick,10,'...',30,ENT_QUOTES);?>');
            <?php endif; ?>
	});
</script>

<div class="position-absolute" style="right: 0">
    <?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>
</div>

<div role="tabpanel" id="tabs">
        <ul class="nav nav-pills" role="tablist">
        </ul>
        <div class="tab-content ps-1">
        </div>
</div>

<script type="text/javascript">
function addChat(chat_id,name)
{
	lhinst.setCloseWindowOnEvent(true);
	lhinst.setDisableRemember(true);
    lhinst.startChat(chat_id,$('#tabs'),name);
    window.focus();
}
</script>