<?php include(erLhcoreClassDesign::designtpl('lhcobrowse/part/browse/browse_toolbar.tpl.php')); ?>

<div id="contentWrap">
<div class="container-fluid h100proc">
<div class="row h100proc">
    <div class="col-xs-3 pr-0 h100proc" id="cobrose-chat-window">
        <?php $chat_id = $chat->id;$chat_to_load = $chat;?>
        <?php include(erLhcoreClassDesign::designtpl('lhchat/single.tpl.php')); ?>        
    </div>
    <div class="columns col-xs-9 h100proc">        
        	<div id="center-layout">
                <iframe id="content" name="content" src="<?php echo erLhcoreClassDesign::baseurl('cobrowse/mirror')?>" frameborder="0"></iframe>
            </div>       
    </div>
</div>
</div>


</div>


<script>
<?php include(erLhcoreClassDesign::designtpl('lhcobrowse/operatorinit.tpl.php')); ?>
</script>