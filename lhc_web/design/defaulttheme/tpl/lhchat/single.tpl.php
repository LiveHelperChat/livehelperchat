
<?php include(erLhcoreClassDesign::designtpl('lhchat/user_settings.tpl.php'));?>

<div role="tabpanel" id="tabs" <?php if (is_numeric($chat_id)) : ?>ng-init="lhc.startChatDashboard(<?php echo (int)$chat_id?>,{'remember' : true})"<?php endif; ?>>
    <ul class="nav nav-pills" role="tablist">
        <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/chats_dashboard_list_tab.tpl.php')); ?>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane form-group" id="chatdashboard">
            <?php include(erLhcoreClassDesign::designtpl('lhchat/lists/chats_dashboard_list.tpl.php')); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $( document ).ready(function() {
        $('#tabs a[href="#chatdashboard"]').tab('show');
    });
</script>